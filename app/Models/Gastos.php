<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gastos extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'caixa_id',
        'categoria_id',
        'valor_total',
        'descricao',
        'data_gasto',
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
}
