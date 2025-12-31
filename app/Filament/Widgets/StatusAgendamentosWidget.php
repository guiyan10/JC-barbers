<?php

namespace App\Filament\Widgets;

use App\Models\Agendamento;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatusAgendamentosWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    public function getHeading(): string
    {
        return 'Status dos Agendamentos de Hoje';
    }

    public function getColumnSpan(): int | array
    {
        return [
            'md' => 2,
            'xl' => 1,
        ];
    }

    protected function getStats(): array
    {
        $hoje = Carbon::today();

        $agendados = Agendamento::whereDate('data_hora', $hoje)
            ->where('status', Agendamento::STATUS_AGENDADO)
            ->count();

        $emAtendimento = Agendamento::whereDate('data_hora', $hoje)
            ->where('status', Agendamento::STATUS_INICIADO)
            ->count();

        $finalizados = Agendamento::whereDate('data_hora', $hoje)
            ->where('status', Agendamento::STATUS_FINALIZADO)
            ->count();

        $total = $agendados + $emAtendimento + $finalizados;

        return [
            Stat::make('Agendados', $agendados)
                ->description($total > 0 ? round(($agendados / $total) * 100, 1) . '% do total' : '0%')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Em Atendimento', $emAtendimento)
                ->description($total > 0 ? round(($emAtendimento / $total) * 100, 1) . '% do total' : '0%')
                ->descriptionIcon('heroicon-m-play-circle')
                ->color('info'),

            Stat::make('Finalizados', $finalizados)
                ->description($total > 0 ? round(($finalizados / $total) * 100, 1) . '% do total' : '0%')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}

