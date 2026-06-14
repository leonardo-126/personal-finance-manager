<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FontesRenda extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fontes_renda';

    protected $fillable = [
        'user_id',
        'nome',
        'tipo',
        'descricao',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
