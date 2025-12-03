<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'telefone',
        'email',
        'data_nascimento',
        'observacoes',
        'plano_id',
        'data_inicio_plano',
        'data_fim_plano',
        'cortes_utilizados_mes',
        'ultimo_reset_contador',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'data_inicio_plano' => 'date',
        'data_fim_plano' => 'date',
        'ultimo_reset_contador' => 'date',
        'cortes_utilizados_mes' => 'integer',
    ];

    public function plano(): BelongsTo
    {
        return $this->belongsTo(Plano::class);
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }

    /**
     * Verifica se o cliente possui plano ativo
     */
    public function temPlanoAtivo(): bool
    {
        if (!$this->plano_id) {
            return false;
        }

        if (!$this->data_fim_plano) {
            return false;
        }

        return Carbon::today()->lte($this->data_fim_plano);
    }

    /**
     * Verifica se o cliente pode usar o plano (tem cortes disponíveis)
     */
    public function podeLimitePlano(): bool
    {
        if (!$this->temPlanoAtivo()) {
            return false;
        }

        $this->resetarContadorSeNecessario();

        return $this->cortes_utilizados_mes < $this->plano->limite_mensal;
    }

    /**
     * Reseta o contador de cortes se mudou o mês
     */
    public function resetarContadorSeNecessario(): void
    {
        if (!$this->ultimo_reset_contador) {
            $this->ultimo_reset_contador = Carbon::today();
            $this->cortes_utilizados_mes = 0;
            $this->save();
            return;
        }

        $ultimoReset = Carbon::parse($this->ultimo_reset_contador);
        $hoje = Carbon::today();

        // Se mudou o mês, reseta o contador
        if ($ultimoReset->month !== $hoje->month || $ultimoReset->year !== $hoje->year) {
            $this->cortes_utilizados_mes = 0;
            $this->ultimo_reset_contador = $hoje;
            $this->save();
        }
    }

    /**
     * Incrementa o contador de cortes utilizados
     */
    public function incrementarCortesUtilizados(): void
    {
        $this->resetarContadorSeNecessario();
        $this->increment('cortes_utilizados_mes');
    }

    /**
     * Retorna a data da última visita
     */
    public function getDataUltimaVisita(): ?Carbon
    {
        $ultimoAgendamento = $this->agendamentos()
            ->where('status', 2) // Finalizado
            ->orderBy('data_hora', 'desc')
            ->first();

        return $ultimoAgendamento ? Carbon::parse($ultimoAgendamento->data_hora) : null;
    }
}



