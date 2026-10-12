<?php

namespace App\Services\OpenFinance\Providers;

use App\Enums\StatusConexaoOpenFinance;
use App\Enums\TipoContaOpenFinance;
use App\Services\OpenFinance\Data\ConexaoData;
use App\Services\OpenFinance\Data\ContaData;
use App\Services\OpenFinance\Data\TransacaoData;
use App\Services\OpenFinance\OpenFinanceException;
use App\Services\OpenFinance\OpenFinanceProvider;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Driver da Pluggy (https://docs.pluggy.ai). A API key gerada em /auth vale
 * 2h; guardamos em cache por um pouco menos e renovamos ao receber 401/403.
 */
class PluggyProvider implements OpenFinanceProvider
{
    private const CACHE_API_KEY = 'open_finance:pluggy:api_key';
    private const PAGE_SIZE = 500;

    public function __construct(private readonly array $config)
    {
        if (empty($config['client_id']) || empty($config['client_secret'])) {
            throw new OpenFinanceException('PLUGGY_CLIENT_ID e PLUGGY_CLIENT_SECRET precisam estar configurados.');
        }
    }

    public function nome(): string
    {
        return 'pluggy';
    }

    public function criarTokenConexao(int $userId, ?string $externalId = null): string
    {
        $options = array_filter([
            'clientUserId' => (string) $userId,
            'webhookUrl'   => config('open_finance.webhook.url'),
        ]);

        $body = array_filter(['itemId' => $externalId, 'options' => $options]);

        return (string) $this->request('post', '/connect_token', $body)->json('accessToken');
    }

    public function buscarConexao(string $externalId): ConexaoData
    {
        $item = $this->request('get', "/items/{$externalId}")->json();

        return new ConexaoData(
            externalId: (string) $item['id'],
            status: $this->mapearStatus($item['status'] ?? null),
            instituicaoId: isset($item['connector']['id']) ? (string) $item['connector']['id'] : null,
            instituicaoNome: $item['connector']['name'] ?? null,
            instituicaoLogo: $item['connector']['imageUrl'] ?? null,
            erro: $item['error']['message'] ?? null,
            consentimentoExpiraEm: isset($item['consentExpiresAt']) ? Carbon::parse($item['consentExpiresAt']) : null,
            clientUserId: $item['clientUserId'] ?? null,
        );
    }

    public function listarContas(string $externalId): array
    {
        $contas = $this->request('get', '/accounts', ['itemId' => $externalId])->json('results', []);

        return array_map(fn (array $conta): ContaData => new ContaData(
            externalId: (string) $conta['id'],
            tipo: $this->mapearTipoConta($conta['type'] ?? null, $conta['subtype'] ?? null),
            nome: $conta['marketingName'] ?? $conta['name'] ?? 'Conta',
            numero: $conta['number'] ?? null,
            saldo: isset($conta['balance']) ? (float) $conta['balance'] : null,
            moeda: $conta['currencyCode'] ?? 'BRL',
        ), $contas);
    }

    public function listarTransacoes(string $contaExternalId, CarbonInterface $de, CarbonInterface $ate): iterable
    {
        $pagina = 1;

        do {
            $resposta = $this->request('get', '/transactions', [
                'accountId' => $contaExternalId,
                'from'      => $de->toDateString(),
                'to'        => $ate->toDateString(),
                'pageSize'  => self::PAGE_SIZE,
                'page'      => $pagina,
            ]);

            foreach ($resposta->json('results', []) as $transacao) {
                // O sinal de `amount` varia por tipo de conta; `type` é a fonte confiável do sentido.
                yield new TransacaoData(
                    externalId: (string) $transacao['id'],
                    data: Carbon::parse($transacao['date']),
                    descricao: trim($transacao['description'] ?? '') ?: 'Sem descrição',
                    valor: abs((float) $transacao['amount']),
                    tipo: ($transacao['type'] ?? 'DEBIT') === 'CREDIT' ? 'credito' : 'debito',
                    categoria: $transacao['category'] ?? null,
                    confirmada: ($transacao['status'] ?? 'POSTED') !== 'PENDING',
                );
            }

            $totalPaginas = (int) $resposta->json('totalPages', 1);
            $pagina++;
        } while ($pagina <= $totalPaginas);
    }

    public function removerConexao(string $externalId): void
    {
        $this->request('delete', "/items/{$externalId}");
    }

    public function conexaoDoWebhook(array $payload): ?string
    {
        // item/deleted: a conexão não existe mais na Pluggy, um sync só falharia.
        if (($payload['event'] ?? null) === 'item/deleted') {
            return null;
        }

        return isset($payload['itemId']) ? (string) $payload['itemId'] : null;
    }

    private function mapearStatus(?string $status): StatusConexaoOpenFinance
    {
        return match ($status) {
            'UPDATED'            => StatusConexaoOpenFinance::Ativa,
            'UPDATING'           => StatusConexaoOpenFinance::Atualizando,
            'LOGIN_ERROR'        => StatusConexaoOpenFinance::ErroLogin,
            'WAITING_USER_INPUT' => StatusConexaoOpenFinance::AguardandoUsuario,
            default              => StatusConexaoOpenFinance::Desatualizada,
        };
    }

    private function mapearTipoConta(?string $tipo, ?string $subtipo): TipoContaOpenFinance
    {
        return match (true) {
            $tipo === 'CREDIT'                  => TipoContaOpenFinance::Cartao,
            $subtipo === 'SAVINGS_ACCOUNT'      => TipoContaOpenFinance::Poupanca,
            $subtipo === 'CHECKING_ACCOUNT'     => TipoContaOpenFinance::Corrente,
            default                             => TipoContaOpenFinance::Outra,
        };
    }

    private function request(string $metodo, string $caminho, array $dados = [], bool $renovarChave = true): Response
    {
        try {
            $resposta = $this->cliente()
                ->withHeaders(['X-API-KEY' => $this->apiKey()])
                ->{$metodo}($caminho, $dados);
        } catch (ConnectionException $e) {
            throw new OpenFinanceException('Não foi possível falar com a Pluggy: '.$e->getMessage(), previous: $e);
        }

        if (in_array($resposta->status(), [401, 403], true) && $renovarChave) {
            Cache::forget(self::CACHE_API_KEY);

            return $this->request($metodo, $caminho, $dados, renovarChave: false);
        }

        try {
            return $resposta->throw();
        } catch (RequestException $e) {
            throw new OpenFinanceException(
                "Pluggy respondeu {$resposta->status()} em {$caminho}: ".($resposta->json('message') ?? 'erro desconhecido'),
                previous: $e,
            );
        }
    }

    private function apiKey(): string
    {
        return Cache::remember(self::CACHE_API_KEY, now()->addMinutes(100), function (): string {
            try {
                $chave = $this->cliente()->post('/auth', [
                    'clientId'     => $this->config['client_id'],
                    'clientSecret' => $this->config['client_secret'],
                ])->throw()->json('apiKey');
            } catch (ConnectionException|RequestException $e) {
                throw new OpenFinanceException('Falha ao autenticar na Pluggy: '.$e->getMessage(), previous: $e);
            }

            if (! $chave) {
                throw new OpenFinanceException('A Pluggy não devolveu uma apiKey.');
            }

            return $chave;
        });
    }

    private function cliente(): PendingRequest
    {
        return Http::baseUrl($this->config['base_url'])
            ->acceptJson()
            ->asJson()
            ->timeout($this->config['timeout'] ?? 20);
    }
}
