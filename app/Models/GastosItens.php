<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GastosItens extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'gasto_id',
        'nome',
        'valor',
        'motivo',
        'data_transacao',
    ];

    protected $casts = [
        'data_transacao' => 'date:Y-m-d',
    ];

    public function gasto(): BelongsTo
    {
        return $this->belongsTo(Gastos::class, 'gasto_id');
    }

}
