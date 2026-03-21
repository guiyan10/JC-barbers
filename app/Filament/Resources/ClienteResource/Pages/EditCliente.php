<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use App\Models\Plano;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCliente extends EditRecord
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
        $data['data_pagamento_previsto'] = $inicio->copy()->addDays($duracaoDias)->toDateString();

        if (empty($data['dia_pagamento'])) {
            $data['dia_pagamento'] = (int) $inicio->day;
        }

        return $data;
    }
}

