<?php

namespace Tests\Feature;

use App\Enums\StatusConexaoOpenFinance;
use App\Jobs\SincronizarConexaoOpenFinance;
use App\Models\OpenFinanceConexao;
use App\Models\OpenFinanceConta;
use App\Models\OpenFinanceTransacao;
use App\Models\User;
use App\Services\OpenFinance\Data\ConexaoData;
use App\Services\OpenFinance\OpenFinanceProvider;
use App\Services\OpenFinance\Providers\FakeProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OpenFinanceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        config(['open_finance.provider' => 'fake']);
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_registrar_conexao_sincroniza_contas_e_transacoes(): void
    {
        $this->postJson('/api/open-finance/conexoes', ['external_id' => 'item-1'])
            ->assertCreated()
            ->assertJsonPath('data.instituicao_nome', 'Banco Fictício');

        // QUEUE_CONNECTION=sync nos testes: o job já rodou.
        $conexao = OpenFinanceConexao::sole();
        $this->assertSame(StatusConexaoOpenFinance::Ativa, $conexao->status);
        $this->assertNotNull($conexao->ultimo_sync_em);
        $this->assertSame(2, $conexao->contas()->count());
        $this->assertSame(7, OpenFinanceTransacao::count());

        $this->getJson('/api/open-finance/conexoes')
            ->assertOk()
            ->assertJsonFragment(['numero' => '•••• 1111', 'tipo' => 'cartao']);
    }

    public function test_numero_da_conta_fica_criptografado_no_banco(): void
    {
        $this->postJson('/api/open-finance/conexoes', ['external_id' => 'item-1']);

        $bruto = OpenFinanceConta::query()->toBase()->where('external_id', 'item-1-cartao')->value('numero');
        $this->assertNotSame('5555444433331111', $bruto);
        $this->assertSame('5555444433331111', OpenFinanceConta::where('external_id', 'item-1-cartao')->value('numero'));
    }

    public function test_sincronizar_de_novo_nao_duplica_e_preserva_decisao_do_usuario(): void
    {
        $this->postJson('/api/open-finance/conexoes', ['external_id' => 'item-1']);
        $transacao = OpenFinanceTransacao::first();

        $this->patchJson("/api/open-finance/transacoes/{$transacao->id}", ['status' => 'ignorada'])->assertOk();

        $conexao = OpenFinanceConexao::sole();
        $this->postJson("/api/open-finance/conexoes/{$conexao->id}/sincronizar")->assertStatus(202);

        $this->assertSame(7, OpenFinanceTransacao::count());
        $this->assertSame('ignorada', $transacao->fresh()->status->value);
    }

    public function test_importar_transacoes_do_cartao_cria_fatura(): void
    {
        $this->postJson('/api/open-finance/conexoes', ['external_id' => 'item-1']);
        $caixa = $this->user->caixasFinanceiras()->create(['nome' => 'Conta']);
        $categoria = $this->user->categoriasGastos()->create(['nome' => 'Cartão']);
        $cartao = OpenFinanceConta::where('tipo', 'cartao')->sole();
        $ids = $cartao->transacoes()->pluck('id')->all();

        $this->postJson("/api/open-finance/contas/{$cartao->id}/importar-fatura", [
            'transacao_ids' => $ids,
            'caixa_id'      => $caixa->id,
            'categoria_id'  => $categoria->id,
        ])
            ->assertCreated()
            ->assertJsonCount(4, 'data.itens');

        // 312.90 + 180.00 + 39.90 - 39.90 (estorno)
        $this->assertEqualsWithDelta(492.90, (float) $this->user->gastos()->sole()->valor_total, 0.001);

        $this->assertSame(0, $cartao->transacoes()->where('status', 'pendente')->count());
        $this->assertSame(0, $cartao->transacoes()->whereNull('gasto_item_id')->count());

        // Importar de novo não duplica: nada mais está pendente.
        $this->postJson("/api/open-finance/contas/{$cartao->id}/importar-fatura", [
            'transacao_ids' => $ids,
            'caixa_id'      => $caixa->id,
            'categoria_id'  => $categoria->id,
        ])->assertUnprocessable();
    }

    public function test_importar_rejeita_caixa_de_outro_usuario(): void
    {
        $this->postJson('/api/open-finance/conexoes', ['external_id' => 'item-1']);
        $outro = User::factory()->create();
        $caixaAlheio = $outro->caixasFinanceiras()->create(['nome' => 'Alheio']);
        $categoria = $this->user->categoriasGastos()->create(['nome' => 'Cartão']);
        $cartao = OpenFinanceConta::where('tipo', 'cartao')->sole();

        $this->postJson("/api/open-finance/contas/{$cartao->id}/importar-fatura", [
            'transacao_ids' => $cartao->transacoes()->pluck('id')->all(),
            'caixa_id'      => $caixaAlheio->id,
            'categoria_id'  => $categoria->id,
        ])->assertUnprocessable()->assertJsonValidationErrors('caixa_id');
    }

    public function test_usuario_nao_ve_nem_adota_conexao_de_outro(): void
    {
        $this->postJson('/api/open-finance/conexoes', ['external_id' => 'item-1']);
        $conexao = OpenFinanceConexao::sole();

        $this->actingAs(User::factory()->create());

        $this->getJson('/api/open-finance/conexoes')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/open-finance/transacoes')->assertOk()->assertJsonCount(0, 'data');
        $this->deleteJson("/api/open-finance/conexoes/{$conexao->id}")->assertNotFound();
        $this->postJson('/api/open-finance/conexoes', ['external_id' => 'item-1'])->assertForbidden();
    }

    public function test_rejeita_conexao_criada_para_outro_usuario_no_agregador(): void
    {
        $this->app->instance(OpenFinanceProvider::class, new class extends FakeProvider {
            public function buscarConexao(string $externalId): ConexaoData
            {
                return new ConexaoData($externalId, StatusConexaoOpenFinance::Ativa, clientUserId: '999999');
            }
        });

        $this->postJson('/api/open-finance/conexoes', ['external_id' => 'item-x'])->assertForbidden();
        $this->assertSame(0, OpenFinanceConexao::count());
    }

    public function test_webhook_exige_segredo_e_agenda_sync(): void
    {
        $this->postJson('/api/open-finance/conexoes', ['external_id' => 'item-1']);
        config(['open_finance.webhook.secret' => 's3gr3d0']);
        Queue::fake();

        $payload = ['event' => 'transactions/created', 'itemId' => 'item-1'];

        $this->postJson('/api/webhooks/open-finance', $payload)->assertUnauthorized();
        $this->postJson('/api/webhooks/open-finance', $payload, ['X-Webhook-Secret' => 'errado'])->assertUnauthorized();
        Queue::assertNothingPushed();

        $this->postJson('/api/webhooks/open-finance', $payload, ['X-Webhook-Secret' => 's3gr3d0'])->assertNoContent();
        Queue::assertPushed(SincronizarConexaoOpenFinance::class);
    }

    public function test_webhook_sem_segredo_configurado_recusa_tudo(): void
    {
        config(['open_finance.webhook.secret' => null]);

        $this->postJson('/api/webhooks/open-finance', ['itemId' => 'x'], ['X-Webhook-Secret' => ''])->assertUnauthorized();
    }

    public function test_remover_conexao_apaga_staging_mas_mantem_fatura(): void
    {
        $this->postJson('/api/open-finance/conexoes', ['external_id' => 'item-1']);
        $caixa = $this->user->caixasFinanceiras()->create(['nome' => 'Conta']);
        $categoria = $this->user->categoriasGastos()->create(['nome' => 'Cartão']);
        $cartao = OpenFinanceConta::where('tipo', 'cartao')->sole();
        $this->postJson("/api/open-finance/contas/{$cartao->id}/importar-fatura", [
            'transacao_ids' => $cartao->transacoes()->pluck('id')->all(),
            'caixa_id'      => $caixa->id,
            'categoria_id'  => $categoria->id,
        ])->assertCreated();

        $this->deleteJson('/api/open-finance/conexoes/'.OpenFinanceConexao::sole()->id)->assertNoContent();

        $this->assertSame(0, OpenFinanceTransacao::count());
        $this->assertSame(1, $this->user->gastos()->where('is_fatura', true)->count());
    }
}
