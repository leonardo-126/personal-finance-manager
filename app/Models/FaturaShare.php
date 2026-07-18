<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Link de compartilhamento de uma fatura com uma pessoa específica.
 * O `token` é a credencial pública que dá acesso somente àquela fatura,
 * já identificando quem é a pessoa (para ela marcar os itens dela).
 */
class FaturaShare extends Model
{
    use HasFactory;

    protected $table = 'fatura_shares';

    protected $fillable = [
        'gasto_id',
        'pessoa_id',
        'token',
    ];

    /** Gera um token opaco único para um novo compartilhamento. */
    public static function gerarToken(): string
    {
        return Str::random(48);
    }

    public function gasto(): BelongsTo
    {
        return $this->belongsTo(Gastos::class, 'gasto_id');
    }

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoas::class, 'pessoa_id');
    }
}
