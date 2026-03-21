<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use App\Models\Plano;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreateCliente extends CreateRecord
{
    protected static string $resource = ClienteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['plano_id']) || empty($data['data_inicio_plano'])) {
            return $data;
        }

        $plano = Plano::find($data['plano_id']);

        if (! $plano) {
            return $data;
        }

        $inicio = Carbon::parse($data['data_inicio_plano']);
        $duracaoDias = (int) ($plano->duracao_dias ?? 30);

        $data['data_fim_plano'] = $inicio->copy()->addDays($duracaoDias)->toDateString();

        // "Último pagamento" deve representar o pagamento já realizado.
        // Ao criar cliente com início de plano, consideramos o primeiro pagamento nessa data.
        $data['data_ultimo_pagamento'] = $inicio->toDateString();
        $data['data_pagamento_previsto'] = $inicio->copy()->addDays($duracaoDias)->toDateString();

        if (empty($data['dia_pagamento'])) {
            $data['dia_pagamento'] = (int) $inicio->day;
        }

        return $data;
    }
}

