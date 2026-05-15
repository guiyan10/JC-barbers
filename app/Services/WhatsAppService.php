<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsAppService
{
    public function notificarBarbearia(string $mensagem): void
    {
        $instanceId  = env('ZAPI_INSTANCE_ID');
        $token       = env('ZAPI_TOKEN');
        $clientToken = env('ZAPI_CLIENT_TOKEN');
        $numero      = env('WHATSAPP_BARBEARIA', '5512981081738');

        if (! $instanceId || ! $token || ! $clientToken) {
            return;
        }

        try {
            Http::withHeaders(['Client-Token' => $clientToken])
                ->post("https://api.z-api.io/instances/{$instanceId}/token/{$token}/send-text", [
                    'phone'   => $numero,
                    'message' => $mensagem,
                ]);
        } catch (Throwable $e) {
            Log::warning('Falha ao enviar WhatsApp para barbearia.', [
                'erro' => $e->getMessage(),
            ]);
        }
    }
}
