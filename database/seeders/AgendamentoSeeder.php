<?php

namespace Database\Seeders;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Servico;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AgendamentoSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = Cliente::all();
        $servicos = Servico::all();

        if ($clientes->isEmpty() || $servicos->isEmpty()) {
            $this->command->warn('É necessário ter clientes e serviços cadastrados antes de criar agendamentos.');
            return;
        }

        $agendamentos = [];

        // Agendamentos FINALIZADOS (últimos 30 dias) - para faturamento
        for ($i = 0; $i < 45; $i++) {
            $cliente = $clientes->random();
            $servico = $servicos->random();
            $dataHora = Carbon::now()->subDays(rand(1, 30))->setTime(rand(8, 18), rand(0, 59), 0);
            $horaInicio = $dataHora->copy()->addMinutes(rand(0, 10));
            $horaFim = $horaInicio->copy()->addMinutes($servico->duracao_estimada + rand(0, 15));
            
            // 60% dos agendamentos de clientes com plano são descontados
            $descontadoPlano = $cliente->temPlanoAtivo() && rand(1, 100) <= 60;

            $agendamentos[] = [
                'cliente_id' => $cliente->id,
                'servico_id' => $servico->id,
                'data_hora' => $dataHora,
                'status' => Agendamento::STATUS_FINALIZADO,
                'hora_inicio' => $horaInicio,
                'hora_fim' => $horaFim,
                'descontado_plano' => $descontadoPlano,
                'observacoes' => rand(1, 10) <= 2 ? 'Cliente pontual' : null,
                'created_at' => $dataHora,
                'updated_at' => $horaFim,
            ];
        }

        // Agendamentos FINALIZADOS (hoje) - para dashboard
        for ($i = 0; $i < 8; $i++) {
            $cliente = $clientes->random();
            $servico = $servicos->random();
            $dataHora = Carbon::today()->setTime(rand(8, 17), rand(0, 59), 0);
            $horaInicio = $dataHora->copy()->addMinutes(rand(0, 5));
            $horaFim = $horaInicio->copy()->addMinutes($servico->duracao_estimada + rand(0, 10));
            
            $descontadoPlano = $cliente->temPlanoAtivo() && rand(1, 100) <= 50;

            $agendamentos[] = [
                'cliente_id' => $cliente->id,
                'servico_id' => $servico->id,
                'data_hora' => $dataHora,
                'status' => Agendamento::STATUS_FINALIZADO,
                'hora_inicio' => $horaInicio,
                'hora_fim' => $horaFim,
                'descontado_plano' => $descontadoPlano,
                'created_at' => $dataHora,
                'updated_at' => $horaFim,
            ];
        }

        // Agendamentos EM ATENDIMENTO (hoje)
        for ($i = 0; $i < 2; $i++) {
            $cliente = $clientes->random();
            $servico = $servicos->random();
            $dataHora = Carbon::now()->subMinutes(rand(10, 60));
            $horaInicio = $dataHora->copy();
            
            $descontadoPlano = $cliente->temPlanoAtivo() && rand(1, 100) <= 70;

            $agendamentos[] = [
                'cliente_id' => $cliente->id,
                'servico_id' => $servico->id,
                'data_hora' => $dataHora,
                'status' => Agendamento::STATUS_INICIADO,
                'hora_inicio' => $horaInicio,
                'hora_fim' => null,
                'descontado_plano' => $descontadoPlano,
                'created_at' => $dataHora,
                'updated_at' => $horaInicio,
            ];
        }

        // Agendamentos AGENDADOS (hoje - próximas horas)
        for ($i = 0; $i < 5; $i++) {
            $cliente = $clientes->random();
            $servico = $servicos->random();
            $dataHora = Carbon::now()->addHours(rand(1, 6))->setMinutes(0);
            
            $agendamentos[] = [
                'cliente_id' => $cliente->id,
                'servico_id' => $servico->id,
                'data_hora' => $dataHora,
                'status' => Agendamento::STATUS_AGENDADO,
                'hora_inicio' => null,
                'hora_fim' => null,
                'descontado_plano' => false,
                'created_at' => Carbon::now()->subDays(rand(1, 7)),
                'updated_at' => Carbon::now()->subDays(rand(1, 7)),
            ];
        }

        // Agendamentos AGENDADOS (próximos dias)
        for ($i = 0; $i < 20; $i++) {
            $cliente = $clientes->random();
            $servico = $servicos->random();
            $dataHora = Carbon::now()->addDays(rand(1, 14))->setTime(rand(8, 18), rand(0, 59), 0);
            
            $agendamentos[] = [
                'cliente_id' => $cliente->id,
                'servico_id' => $servico->id,
                'data_hora' => $dataHora,
                'status' => Agendamento::STATUS_AGENDADO,
                'hora_inicio' => null,
                'hora_fim' => null,
                'descontado_plano' => false,
                'created_at' => Carbon::now()->subDays(rand(1, 10)),
                'updated_at' => Carbon::now()->subDays(rand(1, 10)),
            ];
        }

        // Inserir todos os agendamentos
        foreach ($agendamentos as $agendamento) {
            Agendamento::create($agendamento);
        }
    }
}

