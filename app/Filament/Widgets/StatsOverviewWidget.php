<?php

namespace App\Filament\Widgets;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Servico;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public ?string $periodo = 'month';

    public function getColumnSpan(): int | array
    {
        return [
            'md' => 2,
            'xl' => 1,
        ];
    }

    protected function getStats(): array
    {
        $periodo = $this->getPeriodo();
        $inicio = $periodo['inicio'];
        $fim = $periodo['fim'];

        // Total de atendimentos finalizados no período
        $totalAtendimentos = Agendamento::where('status', Agendamento::STATUS_FINALIZADO)
            ->whereBetween('data_hora', [$inicio, $fim])
            ->count();

        // Total de clientes
        $totalClientes = Cliente::count();

        // Clientes com plano ativo
        $clientesComPlano = Cliente::whereNotNull('plano_id')
            ->where('data_fim_plano', '>=', Carbon::today())
            ->count();

        $percentualComPlano = $totalClientes > 0 
            ? round(($clientesComPlano / $totalClientes) * 100, 1) 
            : 0;

        // Valor faturado (apenas atendimentos que NÃO foram descontados do plano)
        $valorFaturado = Agendamento::where('status', Agendamento::STATUS_FINALIZADO)
            ->where('descontado_plano', false)
            ->whereBetween('data_hora', [$inicio, $fim])
            ->join('servicos', 'agendamentos.servico_id', '=', 'servicos.id')
            ->sum('servicos.preco');

        // Comparação com período anterior
        $periodoAnterior = $this->getPeriodoAnterior($inicio, $fim);
        $totalAtendimentosAnterior = Agendamento::where('status', Agendamento::STATUS_FINALIZADO)
            ->whereBetween('data_hora', [$periodoAnterior['inicio'], $periodoAnterior['fim']])
            ->count();

        $variacaoAtendimentos = $totalAtendimentosAnterior > 0
            ? round((($totalAtendimentos - $totalAtendimentosAnterior) / $totalAtendimentosAnterior) * 100, 1)
            : 0;

        $valorFaturadoAnterior = Agendamento::where('status', Agendamento::STATUS_FINALIZADO)
            ->where('descontado_plano', false)
            ->whereBetween('data_hora', [$periodoAnterior['inicio'], $periodoAnterior['fim']])
            ->join('servicos', 'agendamentos.servico_id', '=', 'servicos.id')
            ->sum('servicos.preco');

        $variacaoFaturamento = $valorFaturadoAnterior > 0
            ? round((($valorFaturado - $valorFaturadoAnterior) / $valorFaturadoAnterior) * 100, 1)
            : 0;

        return [
            Stat::make('Total de Atendimentos', $totalAtendimentos)
                ->description($this->getPeriodoLabel())
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary')
                ->chart($this->getAtendimentosChartData($inicio, $fim))
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Total de Clientes', $totalClientes)
                ->description("{$clientesComPlano} com plano ativo ({$percentualComPlano}%)")
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Clientes com Plano', $clientesComPlano)
                ->description("{$percentualComPlano}% do total")
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('info'),

            Stat::make('Valor Faturado', 'R$ ' . number_format($valorFaturado, 2, ',', '.'))
                ->description($variacaoFaturamento != 0 
                    ? ($variacaoFaturamento > 0 ? '+' : '') . $variacaoFaturamento . '% vs período anterior'
                    : 'Sem variação')
                ->descriptionIcon($variacaoFaturamento >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($variacaoFaturamento >= 0 ? 'success' : 'danger'),
        ];
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

    protected function getPeriodoLabel(): string
    {
        $filtro = $this->periodo ?? 'month';

        return match($filtro) {
            'today' => 'Hoje',
            'week' => 'Esta semana',
            'month' => 'Este mês',
            'quarter' => 'Este trimestre',
            default => 'Este mês',
        };
    }

    protected function getPeriodoAnterior($inicio, $fim): array
    {
        $dias = Carbon::parse($inicio)->diffInDays(Carbon::parse($fim));

        return [
            'inicio' => Carbon::parse($inicio)->subDays($dias + 1),
            'fim' => Carbon::parse($inicio)->subDay(),
        ];
    }

    protected function getAtendimentosChartData($inicio, $fim): array
    {
        $dias = Carbon::parse($inicio)->diffInDays(Carbon::parse($fim));
        
        // Se for mais de 30 dias, agrupar por semana, senão por dia
        if ($dias > 30) {
            $data = [];
            $semanaAtual = Carbon::parse($inicio)->startOfWeek();
            $fimSemana = Carbon::parse($fim);

            while ($semanaAtual->lte($fimSemana)) {
                $fimSemanaAtual = $semanaAtual->copy()->endOfWeek();
                if ($fimSemanaAtual->gt($fimSemana)) {
                    $fimSemanaAtual = $fimSemana;
                }

                $data[] = Agendamento::where('status', Agendamento::STATUS_FINALIZADO)
                    ->whereBetween('data_hora', [$semanaAtual, $fimSemanaAtual])
                    ->count();

                $semanaAtual->addWeek();
            }

            return $data;
        } else {
            $data = [];
            $dataAtual = Carbon::parse($inicio);
            $fimData = Carbon::parse($fim);

            while ($dataAtual->lte($fimData)) {
                $data[] = Agendamento::where('status', Agendamento::STATUS_FINALIZADO)
                    ->whereDate('data_hora', $dataAtual)
                    ->count();

                $dataAtual->addDay();
            }

            return $data;
        }
    }

}

