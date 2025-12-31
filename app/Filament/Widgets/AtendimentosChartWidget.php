<?php

namespace App\Filament\Widgets;

use App\Models\Agendamento;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AtendimentosChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    public function getHeading(): string
    {
        return 'Atendimentos por Período';
    }

    public function getColumnSpan(): int | array
    {
        return [
            'md' => 2,
            'xl' => 1,
        ];
    }

    public ?string $periodo = 'month';

    protected function getData(): array
    {
        $periodo = $this->getPeriodo();
        $inicio = $periodo['inicio'];
        $fim = $periodo['fim'];
        $dias = Carbon::parse($inicio)->diffInDays(Carbon::parse($fim));

        // Se for mais de 30 dias, agrupar por semana, senão por dia
        if ($dias > 30) {
            $labels = [];
            $data = [];
            $semanaAtual = Carbon::parse($inicio)->startOfWeek();
            $fimSemana = Carbon::parse($fim);

            while ($semanaAtual->lte($fimSemana)) {
                $fimSemanaAtual = $semanaAtual->copy()->endOfWeek();
                if ($fimSemanaAtual->gt($fimSemana)) {
                    $fimSemanaAtual = $fimSemana;
                }

                $labels[] = $semanaAtual->format('d/m') . ' - ' . $fimSemanaAtual->format('d/m');
                $data[] = Agendamento::where('status', Agendamento::STATUS_FINALIZADO)
                    ->whereBetween('data_hora', [$semanaAtual, $fimSemanaAtual])
                    ->count();

                $semanaAtual->addWeek();
            }
        } else {
            $labels = [];
            $data = [];
            $dataAtual = Carbon::parse($inicio);
            $fimData = Carbon::parse($fim);

            while ($dataAtual->lte($fimData)) {
                $labels[] = $dataAtual->format('d/m');
                $data[] = Agendamento::where('status', Agendamento::STATUS_FINALIZADO)
                    ->whereDate('data_hora', $dataAtual)
                    ->count();

                $dataAtual->addDay();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Atendimentos Finalizados',
                    'data' => $data,
                    'backgroundColor' => 'rgba(251, 191, 36, 0.2)',
                    'borderColor' => 'rgb(251, 191, 36)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getPeriodo(): array
    {
        $filtro = $this->periodo ?? 'month';

        return match($filtro) {
            'today' => [
                'inicio' => Carbon::today()->startOfDay(),
                'fim' => Carbon::today()->endOfDay(),
            ],
            'week' => [
                'inicio' => Carbon::now()->startOfWeek(),
                'fim' => Carbon::now()->endOfWeek(),
            ],
            'month' => [
                'inicio' => Carbon::now()->startOfMonth(),
                'fim' => Carbon::now()->endOfMonth(),
            ],
            'quarter' => [
                'inicio' => Carbon::now()->startOfQuarter(),
                'fim' => Carbon::now()->endOfQuarter(),
            ],
            default => [
                'inicio' => Carbon::now()->startOfMonth(),
                'fim' => Carbon::now()->endOfMonth(),
            ],
        };
    }

}

