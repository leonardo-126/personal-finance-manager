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
    ];

    public function gasto(): BelongsTo
    {
        return $this->belongsTo(Gastos::class, 'gasto_id');
    }

}
