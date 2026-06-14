<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function fontesRenda(): HasMany
    {
        return $this->hasMany(FontesRenda::class);
    }
    public function caixasFinanceiras(): HasMany
    {
        return $this->hasMany(CaixasFinanceiras::class);
    }
    public function rendas(): HasMany
    {
        return $this->hasMany(Rendas::class);
    }
    public function movimentacoesCaixas(): HasMany
    {
        return $this->hasMany(MovimentacoesCaixas::class);
    }
    public function categoriasGastos(): HasMany
    {
        return $this->hasMany(CategoriasGastos::class);
    }
    public function gastos(): HasMany
    {
        return $this->hasMany(Gastos::class);
    }
    public function gastosItens(): HasManyThrough
    {
        return $this->hasManyThrough(GastosItens::class, Gastos::class, 'user_id', 'gasto_id');
    }
}
