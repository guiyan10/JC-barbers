<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plano extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'valor',
        'limite_mensal',
        'duracao_dias',
        'ativo',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'limite_mensal' => 'integer',
        'duracao_dias' => 'integer',
        'ativo' => 'boolean',
    ];

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }
}



