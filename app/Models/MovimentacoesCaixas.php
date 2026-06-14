<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimentacoesCaixas extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'caixa_id',
        'renda_id',
        'valor',
        'tipo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function caixa(): BelongsTo
    {
        return $this->belongsTo(CaixasFinanceiras::class, 'caixa_id');
    }

    public function renda(): BelongsTo
    {
        return $this->belongsTo(Rendas::class, 'renda_id');
    }
}
