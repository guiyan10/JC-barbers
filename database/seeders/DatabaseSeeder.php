<?php

namespace Database\Seeders;

use App\Models\Plano;
use App\Models\Servico;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Criar usuário administrador
        $this->call(UserSeeder::class);

        // Criar Plano Clássico (apenas se não existir)
        if (Plano::count() === 0) {
            Plano::create([
                'nome' => 'Plano Clássico',
                'descricao' => 'Plano mensal com 4 cortes inclusos',
                'valor' => 74.90,
                'limite_mensal' => 4,
                'ativo' => true,
            ]);
        }

        // Criar serviços padrão (apenas se não existirem)
        if (Servico::count() === 0) {
            $servicos = [
                [
                    'nome' => 'Corte Simples',
                    'descricao' => 'Corte de cabelo tradicional',
                    'preco' => 30.00,
                    'duracao_estimada' => 30,
                    'ativo' => true,
                ],
                [
                    'nome' => 'Corte + Barba',
                    'descricao' => 'Corte de cabelo + barba completa',
                    'preco' => 50.00,
                    'duracao_estimada' => 45,
                    'ativo' => true,
                ],
                [
                    'nome' => 'Barba',
                    'descricao' => 'Barba completa',
                    'preco' => 25.00,
                    'duracao_estimada' => 20,
                    'ativo' => true,
                ],
                [
                    'nome' => 'Acabamento',
                    'descricao' => 'Acabamento de barba e contornos',
                    'preco' => 15.00,
                    'duracao_estimada' => 15,
                    'ativo' => true,
                ],
            ];

            foreach ($servicos as $servico) {
                Servico::create($servico);
            }
        }

        // Criar clientes
        $this->call(ClienteSeeder::class);

        // Criar agendamentos
        $this->call(AgendamentoSeeder::class);
    }
}



