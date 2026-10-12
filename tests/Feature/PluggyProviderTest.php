<?php

namespace Tests\Feature;

use App\Enums\StatusConexaoOpenFinance;
use App\Enums\TipoContaOpenFinance;
use App\Services\OpenFinance\OpenFinanceException;
use App\Services\OpenFinance\Providers\PluggyProvider;
use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PluggyProviderTest extends TestCase
{
    private PluggyProvider $pluggy;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        $this->pluggy = new PluggyProvider([
            'base_url'      => 'https://api.pluggy.test',
            'client_id'     => 'id',
            'client_secret' => 'secret',
        ]);
    }

    public function test_mapeia_item_contas_e_transacoes_paginadas(): void
    {
        Http::fake([
            'api.pluggy.test/auth' => Http::response(['apiKey' => 'chave']),
            'api.pluggy.test/items/item-1' => Http::response([
                'id' => 'item-1', 'status' => 'UPDATED', 'clientUserId' => '7',
                'connector' => ['id' => 201, 'name' => 'Nubank', 'imageUrl' => 'https://logo'],
            ]),
            'api.pluggy.test/accounts*' => Http::response(['results' => [
                ['id' => 'acc-1', 'type' => 'CREDIT', 'subtype' => 'CREDIT_CARD', 'name' => 'Ultravioleta', 'number' => '1234', 'balance' => 100.5, 'currencyCode' => 'BRL'],
                ['id' => 'acc-2', 'type' => 'BANK', 'subtype' => 'CHECKING_ACCOUNT', 'name' => 'Conta', 'balance' => 10],
            ]]),
            'api.pluggy.test/transactions*' => function (Request $request) {
                $pagina = (int) $request['page'];

                return Http::response([
                    'page' => $pagina, 'totalPages' => 2,
                    'results' => [[
                        'id' => "tx-{$pagina}", 'description' => 'Compra', 'amount' => $pagina === 1 ? -50.0 : 20.0,
                        'date' => '2026-10-01T10:00:00.000Z', 'type' => $pagina === 1 ? 'DEBIT' : 'CREDIT',
                        'status' => $pagina === 1 ? 'PENDING' : 'POSTED', 'category' => 'Shopping',
                    ]],
                ]);
            },
        ]);

        $conexao = $this->pluggy->buscarConexao('item-1');
        $this->assertSame(StatusConexaoOpenFinance::Ativa, $conexao->status);
        $this->assertSame('Nubank', $conexao->instituicaoNome);
        $this->assertSame('7', $conexao->clientUserId);

        $contas = $this->pluggy->listarContas('item-1');
        $this->assertSame(TipoContaOpenFinance::Cartao, $contas[0]->tipo);
        $this->assertSame(TipoContaOpenFinance::Corrente, $contas[1]->tipo);

        $transacoes = iterator_to_array($this->pluggy->listarTransacoes('acc-1', Carbon::parse('2026-09-01'), Carbon::parse('2026-10-12')), false);
        $this->assertCount(2, $transacoes);
        $this->assertSame([50.0, 'debito', false], [$transacoes[0]->valor, $transacoes[0]->tipo, $transacoes[0]->confirmada]);
        $this->assertSame([20.0, 'credito', true], [$transacoes[1]->valor, $transacoes[1]->tipo, $transacoes[1]->confirmada]);

        // Uma única autenticação reaproveitada em todas as chamadas.
        Http::assertSentCount(1 + 1 + 1 + 2);
        Http::assertSent(fn (Request $r) => $r->url() === 'https://api.pluggy.test/items/item-1' && $r->header('X-API-KEY') === ['chave']);
    }

    public function test_renova_api_key_expirada(): void
    {
        Cache::put('open_finance:pluggy:api_key', 'expirada');

        Http::fake([
            'api.pluggy.test/auth' => Http::response(['apiKey' => 'nova']),
            'api.pluggy.test/items/*' => fn (Request $r) => $r->header('X-API-KEY') === ['nova']
                ? Http::response(['id' => 'item-1', 'status' => 'UPDATING'])
                : Http::response(['message' => 'expired'], 401),
        ]);

        $this->assertSame(StatusConexaoOpenFinance::Atualizando, $this->pluggy->buscarConexao('item-1')->status);
    }

    public function test_erro_da_api_vira_open_finance_exception(): void
    {
        Http::fake([
            'api.pluggy.test/auth' => Http::response(['apiKey' => 'chave']),
            'api.pluggy.test/*' => Http::response(['message' => 'boom'], 500),
        ]);

        $this->expectException(OpenFinanceException::class);
        $this->pluggy->listarContas('item-1');
    }

    public function test_webhook_ignora_item_deletado(): void
    {
        $this->assertSame('item-1', $this->pluggy->conexaoDoWebhook(['event' => 'item/updated', 'itemId' => 'item-1']));
        $this->assertNull($this->pluggy->conexaoDoWebhook(['event' => 'item/deleted', 'itemId' => 'item-1']));
    }
}
