<?php

namespace App\Filament\Widgets;

use App\Models\Agendamento;
use App\Models\Servico;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ServicosChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    public function getHeading(): string
    {
        return 'Serviços Mais Solicitados';
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

        // Buscar top 5 serviços mais solicitados no período
        $servicos = Servico::withCount([
            'agendamentos' => function ($query) use ($inicio, $fim) {
                $query->where('status', Agendamento::STATUS_FINALIZADO)
                    ->whereBetween('data_hora', [$inicio, $fim]);
            }
        ])
        ->orderBy('agendamentos_count', 'desc')
        ->limit(5)
        ->get();

        $labels = $servicos->pluck('nome')->toArray();
        $data = $servicos->pluck('agendamentos_count')->toArray();

        // Cores para o gráfico
        $colors = [
            'rgba(251, 191, 36, 0.8)',   // Amber
            'rgba(245, 158, 11, 0.8)',   // Amber darker
            'rgba(217, 119, 6, 0.8)',    // Orange
            'rgba(194, 65, 12, 0.8)',    // Orange darker
            'rgba(154, 52, 18, 0.8)',     // Red
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Quantidade de Atendimentos',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => array_map(function($color) {
                        return str_replace('0.8', '1', $color);
                    }, array_slice($colors, 0, count($data))),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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

