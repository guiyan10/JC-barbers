<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Plano;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $plano = Plano::first();

        // Clientes COM plano ativo
        $clientesComPlano = [
            [
                'nome' => 'João Silva',
                'telefone' => '(11) 98765-4321',
                'email' => 'joao.silva@email.com',
                'data_nascimento' => '1990-05-15',
                'plano_id' => $plano->id,
                'data_inicio_plano' => Carbon::now()->subMonths(2),
                'data_fim_plano' => Carbon::now()->addMonths(1),
                'cortes_utilizados_mes' => 2,
                'ultimo_reset_contador' => Carbon::now()->startOfMonth(),
            ],
            [
                'nome' => 'Maria Santos',
                'telefone' => '(11) 97654-3210',
                'email' => 'maria.santos@email.com',
                'data_nascimento' => '1985-08-20',
                'plano_id' => $plano->id,
                'data_inicio_plano' => Carbon::now()->subMonth(),
                'data_fim_plano' => Carbon::now()->addMonths(2),
                'cortes_utilizados_mes' => 1,
                'ultimo_reset_contador' => Carbon::now()->startOfMonth(),
            ],
            [
                'nome' => 'Pedro Oliveira',
                'telefone' => '(11) 96543-2109',
                'email' => 'pedro.oliveira@email.com',
                'data_nascimento' => '1992-03-10',
                'plano_id' => $plano->id,
                'data_inicio_plano' => Carbon::now()->subDays(15),
                'data_fim_plano' => Carbon::now()->addDays(15),
                'cortes_utilizados_mes' => 3,
                'ultimo_reset_contador' => Carbon::now()->startOfMonth(),
            ],
            [
                'nome' => 'Ana Costa',
                'telefone' => '(11) 95432-1098',
                'email' => 'ana.costa@email.com',
                'data_nascimento' => '1988-11-25',
                'plano_id' => $plano->id,
                'data_inicio_plano' => Carbon::now()->subDays(5),
                'data_fim_plano' => Carbon::now()->addMonths(1)->subDays(5),
                'cortes_utilizados_mes' => 0,
                'ultimo_reset_contador' => Carbon::now()->startOfMonth(),
            ],
            [
                'nome' => 'Carlos Mendes',
                'telefone' => '(11) 94321-0987',
                'email' => 'carlos.mendes@email.com',
                'data_nascimento' => '1995-07-30',
                'plano_id' => $plano->id,
                'data_inicio_plano' => Carbon::now()->subMonths(3),
                'data_fim_plano' => Carbon::now()->addDays(10),
                'cortes_utilizados_mes' => 4,
                'ultimo_reset_contador' => Carbon::now()->startOfMonth(),
            ],
        ];

        // Clientes SEM plano
        $clientesSemPlano = [
            [
                'nome' => 'Roberto Alves',
                'telefone' => '(11) 93210-9876',
                'email' => 'roberto.alves@email.com',
                'data_nascimento' => '1987-02-14',
            ],
            [
                'nome' => 'Fernanda Lima',
                'telefone' => '(11) 92109-8765',
                'email' => 'fernanda.lima@email.com',
                'data_nascimento' => '1993-09-05',
            ],
            [
                'nome' => 'Lucas Pereira',
                'telefone' => '(11) 91098-7654',
                'email' => 'lucas.pereira@email.com',
                'data_nascimento' => '1991-12-18',
            ],
            [
                'nome' => 'Juliana Rocha',
                'telefone' => '(11) 90987-6543',
                'email' => 'juliana.rocha@email.com',
                'data_nascimento' => '1989-04-22',
            ],
            [
                'nome' => 'Rafael Souza',
                'telefone' => '(11) 89876-5432',
                'email' => 'rafael.souza@email.com',
                'data_nascimento' => '1994-06-08',
            ],
            [
                'nome' => 'Patricia Ferreira',
                'telefone' => '(11) 88765-4321',
                'email' => 'patricia.ferreira@email.com',
                'data_nascimento' => '1986-10-12',
            ],
            [
                'nome' => 'Bruno Martins',
                'telefone' => '(11) 87654-3210',
                'email' => 'bruno.martins@email.com',
                'data_nascimento' => '1996-01-28',
            ],
            [
                'nome' => 'Camila Rodrigues',
                'telefone' => '(11) 86543-2109',
                'email' => 'camila.rodrigues@email.com',
                'data_nascimento' => '1990-08-15',
            ],
        ];

        // Criar clientes com plano
        foreach ($clientesComPlano as $cliente) {
            Cliente::create($cliente);
        }

        // Criar clientes sem plano
        foreach ($clientesSemPlano as $cliente) {
            Cliente::create($cliente);
        }
    }
}

