<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Agendamento extends Model
{
    use HasFactory;

    const STATUS_AGENDADO = 0;
    const STATUS_INICIADO = 1;
    const STATUS_FINALIZADO = 2;

    protected $fillable = [
        'cliente_id',
        'servico_id',
        'data_hora',
        'status',
        'hora_inicio',
        'hora_fim',
        'observacoes',
        'descontado_plano',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
        'hora_inicio' => 'datetime',
        'hora_fim' => 'datetime',
        'status' => 'integer',
        'descontado_plano' => 'boolean',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servico::class);
    }

    /**
     * Inicia o atendimento
     * IMPORTANTE: Aqui é onde descontamos do plano!
     */
    public function iniciarAtendimento(): void
    {
        $this->status = self::STATUS_INICIADO;
        $this->hora_inicio = Carbon::now();

        // Desconta do plano APENAS quando inicia o atendimento
        if (!$this->descontado_plano && $this->cliente->temPlanoAtivo()) {
            $this->cliente->incrementarCortesUtilizados();
            $this->descontado_plano = true;
        }

        $this->save();
    }

    /**
     * Finaliza o atendimento
     */
    public function finalizarAtendimento(): void
    {
        $this->status = self::STATUS_FINALIZADO;
        $this->hora_fim = Carbon::now();
        $this->save();
    }

    /**
     * Cancela o atendimento e devolve o crédito se foi descontado
     */
    public function cancelarAtendimento(): void
    {
        if ($this->descontado_plano && $this->cliente->temPlanoAtivo()) {
            $this->cliente->decrement('cortes_utilizados_mes');
            $this->descontado_plano = false;
        }

        $this->delete();
    }

    /**
     * Retorna o nome do status
     */
    public function getStatusNomeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_AGENDADO => 'Agendado',
            self::STATUS_INICIADO => 'Em Atendimento',
            self::STATUS_FINALIZADO => 'Finalizado',
            default => 'Desconhecido',
        };
    }

    /**
     * Retorna a cor do status para exibição
     */
    public function getStatusCorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_AGENDADO => 'warning',
            self::STATUS_INICIADO => 'info',
            self::STATUS_FINALIZADO => 'success',
            default => 'secondary',
        };
    }
}



