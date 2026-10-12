<?php

namespace App\Models;

use App\Enums\StatusConexaoOpenFinance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Consentimento do usuário em uma instituição, mantido pelo agregador.
 * `external_id` é o identificador da conexão no agregador (o "item" na Pluggy).
 */
class OpenFinanceConexao extends Model
{
    use HasFactory;

    protected $table = 'open_finance_conexoes';

    protected $fillable = [
        'user_id',
        'provider',
        'external_id',
        'instituicao_id',
        'instituicao_nome',
        'instituicao_logo',
        'status',
        'erro',
        'consentimento_expira_em',
        'ultimo_sync_em',
    ];

    protected $casts = [
        'status'                  => StatusConexaoOpenFinance::class,
        'consentimento_expira_em' => 'datetime',
        'ultimo_sync_em'          => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contas(): HasMany
    {
        return $this->hasMany(OpenFinanceConta::class, 'conexao_id');
    }
}
