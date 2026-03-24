<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $token;

    public function __construct()
    {
        // Se espera que la URL sea algo como: https://api.ultramsg.com/INSTANCE_ID/messages/chat
        $this->apiUrl = env('WHATSAPP_API_URL');
        $this->token = env('WHATSAPP_API_TOKEN');
    }

    /**
     * Enviar mensaje de texto por WhatsApp usando la API (ej. UltraMsg)
     *
     * @param string $to Número telefónico en formato internacional (Ej: +529991234567)
     * @param string $message Contenido del mensaje a enviar
     * @return bool
     */
    public function sendMessage($to, $message)
    {
        if (empty($this->apiUrl) || empty($this->token)) {
            Log::warning("WHATSAPP: Credenciales no configuradas. Simulando envío. Para: {$to} | Mensaje: {$message}");
            return false;
        }

        try {
            // Eliminar caracteres no numéricos pero conservar el '+' inicial si existe
            $formattedNumber = preg_replace('/[^\d+]/', '', $to);

            $response = Http::withoutVerifying()->asForm()->post($this->apiUrl, [
                'token' => $this->token,
                'to' => $formattedNumber,
                'body' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WHATSAPP: Mensaje enviado con éxito a {$formattedNumber}");
                return true;
            }

            Log::error("WHATSAPP: Falló envío a {$formattedNumber}. Error: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("WHATSAPP: Excepción durante el envío: " . $e->getMessage());
            return false;
        }
    }
}
