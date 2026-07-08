<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gastos extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'caixa_id',
        'categoria_id',
        'valor_total',
        'descricao',
        'is_fatura',
        'data_gasto',
    ];

    protected $casts = [
        'is_fatura' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function caixa(): BelongsTo
    {
        return $this->belongsTo(CaixasFinanceiras::class, 'caixa_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriasGastos::class, 'categoria_id');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(GastosItens::class, 'gasto_id');
    }

    /**
     * Recalcula o valor_total como a soma dos itens e persiste.
     * Usado ao criar/editar/remover itens (ex.: manipulação da fatura).
     */
    public function recalcularTotal(): void
    {
        $this->update([
            'valor_total' => (float) $this->itens()->sum('valor'),
        ]);
    }
}
