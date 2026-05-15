<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsAppService
{
    public function notificarBarbearia(string $mensagem): void
    {
        $numero = env('WHATSAPP_BARBEARIA', '5512981081738');
        $apiKey = env('CALLMEBOT_APIKEY');

        if (! $apiKey) {
            return;
        }

        try {
            Http::get('https://api.callmebot.com/whatsapp.php', [
                'phone'  => $numero,
                'text'   => $mensagem,
                'apikey' => $apiKey,
            ]);
        } catch (Throwable $e) {
            Log::warning('Falha ao enviar WhatsApp para barbearia.', [
                'erro' => $e->getMessage(),
            ]);
        }
    }
}
