<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

/**
 * Lê uma fatura de cartão de crédito exportada pelo Nubank e devolve as
 * transações normalizadas. Aceita tanto o CSV (date,title,amount) quanto o
 * arquivo Excel (.xlsx) gerado pelo app.
 */
class NubankFaturaParser
{
    /** Cabeçalhos reconhecidos para cada coluna (normalizados: minúsculos e sem acento). */
    private const COLUNAS_DATA = ['date', 'data'];
    private const COLUNAS_DESCRICAO = ['title', 'titulo', 'descricao', 'description', 'estabelecimento', 'lancamento'];
    private const COLUNAS_VALOR = ['amount', 'valor', 'value'];

    /**
     * @return array<int, array{data: ?string, descricao: string, valor: float}>
     */
    public function parse(UploadedFile $arquivo): array
    {
        $linhas = $this->lerLinhas($arquivo);

        if (empty($linhas)) {
            throw new RuntimeException('O arquivo da fatura está vazio.');
        }

        [$mapa, $inicio] = $this->resolverColunas($linhas);

        $transacoes = [];
        $total = count($linhas);
        for ($i = $inicio; $i < $total; $i++) {
            $linha = $linhas[$i];

            $descricao = trim((string) ($linha[$mapa['descricao']] ?? ''));
            $valorBruto = (string) ($linha[$mapa['valor']] ?? '');

            // Ignora linhas em branco ou de rodapé sem valor.
            if ($descricao === '' && trim($valorBruto) === '') {
                continue;
            }

            $valor = $this->parseValor($valorBruto);
            if ($valor === null) {
                continue;
            }

            $transacoes[] = [
                'data'      => $this->parseData((string) ($linha[$mapa['data']] ?? '')),
                'descricao' => $descricao !== '' ? $descricao : 'Sem descrição',
                'valor'     => $valor,
            ];
        }

        if (empty($transacoes)) {
            throw new RuntimeException('Nenhuma transação válida foi encontrada no arquivo. Confira se é a fatura exportada do Nubank.');
        }

        return $transacoes;
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function lerLinhas(UploadedFile $arquivo): array
    {
        $caminho = $arquivo->getRealPath();

        if ($caminho === false) {
            throw new RuntimeException('Não foi possível acessar o arquivo enviado.');
        }

        return $this->pareceXlsx($caminho)
            ? $this->lerXlsx($caminho)
            : $this->lerCsv($caminho);
    }

    /** Detecta um arquivo .xlsx pela assinatura ZIP ("PK"). */
    private function pareceXlsx(string $caminho): bool
    {
        $handle = @fopen($caminho, 'rb');
        if ($handle === false) {
            return false;
        }
        $assinatura = fread($handle, 2);
        fclose($handle);

        return $assinatura === 'PK';
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function lerCsv(string $caminho): array
    {
        $conteudo = file_get_contents($caminho);
        if ($conteudo === false) {
            throw new RuntimeException('Não foi possível ler o arquivo da fatura.');
        }

        // Remove o BOM UTF-8, se presente.
        $conteudo = preg_replace('/^\xEF\xBB\xBF/', '', $conteudo);

        // Detecta o delimitador a partir da primeira linha (vírgula ou ponto e vírgula).
        $primeiraLinha = strtok($conteudo, "\r\n");
        $primeiraLinha = $primeiraLinha === false ? '' : $primeiraLinha;
        $delimitador = substr_count($primeiraLinha, ';') > substr_count($primeiraLinha, ',') ? ';' : ',';

        $linhas = [];
        foreach (preg_split('/\r\n|\r|\n/', $conteudo) as $linha) {
            if ($linha === '') {
                continue;
            }
            $linhas[] = str_getcsv($linha, $delimitador);
        }

        return $linhas;
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function lerXlsx(string $caminho): array
    {
        $zip = new ZipArchive();
        if ($zip->open($caminho) !== true) {
            throw new RuntimeException('Não foi possível abrir o arquivo Excel da fatura.');
        }

        // Tabela de strings compartilhadas do XLSX.
        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $xml = simplexml_load_string($sharedXml);
            if ($xml !== false) {
                foreach ($xml->si as $si) {
                    $sharedStrings[] = $this->textoDeSi($si);
                }
            }
        }

        // Lê a primeira planilha do arquivo.
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            throw new RuntimeException('A planilha da fatura não pôde ser lida.');
        }

        $xml = simplexml_load_string($sheetXml);
        if ($xml === false || ! isset($xml->sheetData)) {
            throw new RuntimeException('A planilha da fatura está corrompida.');
        }

        $linhas = [];
        foreach ($xml->sheetData->row as $row) {
            $celulas = [];
            $colIndex = 0;
            foreach ($row->c as $c) {
                $coluna = $this->colunaParaIndice((string) $c['r']);

                // Preenche colunas vazias entre células (XLSX omite células em branco).
                while ($colIndex < $coluna) {
                    $celulas[$colIndex] = '';
                    $colIndex++;
                }

                $tipo = (string) $c['t'];
                $valor = (string) $c->v;
                if ($tipo === 's') {
                    $valor = $sharedStrings[(int) $valor] ?? '';
                } elseif ($tipo === 'inlineStr' && isset($c->is)) {
                    $valor = $this->textoDeSi($c->is);
                }

                $celulas[$colIndex] = $valor;
                $colIndex++;
            }
            $linhas[] = $celulas;
        }

        return $linhas;
    }

    /** Extrai o texto de um nó <si>/<is>, lidando com rich text (<r><t>). */
    private function textoDeSi(SimpleXMLElement $no): string
    {
        if (isset($no->t)) {
            return (string) $no->t;
        }

        $texto = '';
        foreach ($no->r as $r) {
            $texto .= (string) $r->t;
        }

        return $texto;
    }

    /** Converte a referência de célula ("B3") no índice da coluna (0-based). */
    private function colunaParaIndice(string $ref): int
    {
        if (! preg_match('/^([A-Z]+)/', $ref, $m)) {
            return 0;
        }

        $letras = $m[1];
        $indice = 0;
        $len = strlen($letras);
        for ($i = 0; $i < $len; $i++) {
            $indice = $indice * 26 + (ord($letras[$i]) - ord('A') + 1);
        }

        return $indice - 1;
    }

    /**
     * Localiza a linha de cabeçalho e mapeia o índice de cada coluna.
     *
     * @param  array<int, array<int, string>>  $linhas
     * @return array{0: array{data: ?int, descricao: int, valor: int}, 1: int}
     */
    private function resolverColunas(array $linhas): array
    {
        foreach ($linhas as $i => $linha) {
            $mapa = ['data' => null, 'descricao' => null, 'valor' => null];

            foreach ($linha as $idx => $valor) {
                $chave = $this->normalizar((string) $valor);
                if ($mapa['data'] === null && in_array($chave, self::COLUNAS_DATA, true)) {
                    $mapa['data'] = $idx;
                } elseif ($mapa['descricao'] === null && in_array($chave, self::COLUNAS_DESCRICAO, true)) {
                    $mapa['descricao'] = $idx;
                } elseif ($mapa['valor'] === null && in_array($chave, self::COLUNAS_VALOR, true)) {
                    $mapa['valor'] = $idx;
                }
            }

            if ($mapa['valor'] !== null && $mapa['descricao'] !== null) {
                return [$mapa, $i + 1];
            }
        }

        // Sem cabeçalho reconhecível: assume a ordem padrão do Nubank (date, title, amount).
        return [['data' => 0, 'descricao' => 1, 'valor' => 2], 0];
    }

    private function normalizar(string $valor): string
    {
        $valor = trim(mb_strtolower($valor));

        $comAcento = ['á', 'à', 'â', 'ã', 'ä', 'é', 'è', 'ê', 'ë', 'í', 'ì', 'î', 'ï', 'ó', 'ò', 'ô', 'õ', 'ö', 'ú', 'ù', 'û', 'ü', 'ç'];
        $semAcento = ['a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'c'];

        return str_replace($comAcento, $semAcento, $valor);
    }

    private function parseValor(string $bruto): ?float
    {
        $limpo = preg_replace('/[^0-9,.\-]/', '', $bruto);
        if ($limpo === null || $limpo === '' || $limpo === '-') {
            return null;
        }

        $ultimaVirgula = strrpos($limpo, ',');
        $ultimoPonto = strrpos($limpo, '.');

        if ($ultimaVirgula !== false && $ultimoPonto !== false) {
            if ($ultimaVirgula > $ultimoPonto) {
                // Formato brasileiro: 1.234,56
                $limpo = str_replace(['.', ','], ['', '.'], $limpo);
            } else {
                // Formato americano: 1,234.56
                $limpo = str_replace(',', '', $limpo);
            }
        } elseif ($ultimaVirgula !== false) {
            $limpo = str_replace(',', '.', $limpo);
        }

        return is_numeric($limpo) ? (float) $limpo : null;
    }

    private function parseData(string $bruto): ?string
    {
        $bruto = trim($bruto);
        if ($bruto === '') {
            return null;
        }

        $formatos = ['Y-m-d', 'd/m/Y', 'd/m/Y H:i', 'd/m/Y H:i:s', 'Y-m-d H:i:s', 'm/d/Y'];
        foreach ($formatos as $formato) {
            try {
                $data = Carbon::createFromFormat($formato, $bruto);
            } catch (\Throwable) {
                continue;
            }
            if ($data !== false) {
                return $data->toDateString();
            }
        }

        try {
            return Carbon::parse($bruto)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
