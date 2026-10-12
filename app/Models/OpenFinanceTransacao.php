<?php

namespace App\Models;

use App\Enums\StatusTransacaoOpenFinance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Transação recebida do banco, aguardando o usuário decidir se vira gasto.
 */
class OpenFinanceTransacao extends Model
{
    use HasFactory;

    protected $table = 'open_finance_transacoes';

    protected $fillable = [
        'conta_id',
        'user_id',
        'external_id',
        'data',
        'descricao',
        'valor',
        'tipo',
        'categoria_externa',
        'confirmada',
        'status',
        'gasto_item_id',
    ];

    protected $casts = [
        'data'       => 'date:Y-m-d',
        'valor'      => 'decimal:2',
        'confirmada' => 'boolean',
        'status'     => StatusTransacaoOpenFinance::class,
    ];

    public function conta(): BelongsTo
    {
        return $this->belongsTo(OpenFinanceConta::class, 'conta_id');
    }

    public function gastoItem(): BelongsTo
    {
        return $this->belongsTo(GastosItens::class, 'gasto_item_id');
    }

    /** Valor com sinal no formato usado pelas faturas: compra positiva, estorno/pagamento negativo. */
    public function valorAssinado(): float
    {
        return $this->tipo === 'debito' ? (float) $this->valor : -(float) $this->valor;
    }
}
