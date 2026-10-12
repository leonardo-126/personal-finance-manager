<?php

namespace App\Models;

use App\Enums\TipoContaOpenFinance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpenFinanceConta extends Model
{
    use HasFactory;

    protected $table = 'open_finance_contas';

    protected $fillable = [
        'conexao_id',
        'user_id',
        'caixa_id',
        'external_id',
        'tipo',
        'nome',
        'numero',
        'saldo',
        'moeda',
    ];

    protected $casts = [
        'tipo'   => TipoContaOpenFinance::class,
        'numero' => 'encrypted',
        'saldo'  => 'decimal:2',
    ];

    public function conexao(): BelongsTo
    {
        return $this->belongsTo(OpenFinanceConexao::class, 'conexao_id');
    }

    public function caixa(): BelongsTo
    {
        return $this->belongsTo(CaixasFinanceiras::class, 'caixa_id');
    }

    public function transacoes(): HasMany
    {
        return $this->hasMany(OpenFinanceTransacao::class, 'conta_id');
    }

    /** Últimos 4 dígitos, para exibir sem expor o número inteiro. */
    public function numeroMascarado(): ?string
    {
        return $this->numero ? '•••• '.substr($this->numero, -4) : null;
    }
}
