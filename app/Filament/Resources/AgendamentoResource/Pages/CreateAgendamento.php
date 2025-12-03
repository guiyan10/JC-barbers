<?php

namespace App\Filament\Resources\AgendamentoResource\Pages;

use App\Filament\Resources\AgendamentoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAgendamento extends CreateRecord
{
    protected static string $resource = AgendamentoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 0; // Sempre criar como agendado
        
        // Se veio cliente_id da URL, preencher
        if (request()->has('cliente_id')) {
            $data['cliente_id'] = request()->get('cliente_id');
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Preencher cliente_id se vier da URL
        if (request()->has('cliente_id')) {
            $data['cliente_id'] = request()->get('cliente_id');
        }
        
        return $data;
    }
}

