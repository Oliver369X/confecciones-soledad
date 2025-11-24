<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class PagoFacilService
{
    protected $baseUrl;
    protected $tokenService;
    protected $tokenSecret;

    public function __construct()
    {
        $this->baseUrl = env('PAGOFACIL_BASE_URL', 'https://masterqr.pagofacil.com.bo/api/services/v2');
        $this->tokenService = env('PAGOFACIL_TOKEN_SERVICE');
        $this->tokenSecret = env('PAGOFACIL_TOKEN_SECRET');
    }

    /**
     * Autenticarse y obtener el Access Token
     */
    public function authenticate()
    {
        \Log::info('🔑 Intentando autenticar con PagoFácil', [
            'url' => "{$this->baseUrl}/login",
            'has_token_service' => !empty($this->tokenService),
            'has_token_secret' => !empty($this->tokenSecret),
        ]);

        $response = Http::withoutVerifying()
            ->timeout(30)
            ->withHeaders([
                'tcTokenService' => $this->tokenService,  // ✅ Como header
                'tcTokenSecret' => $this->tokenSecret,    // ✅ Como header
            ])
            ->post("{$this->baseUrl}/login");

        \Log::info('🔐 Respuesta de autenticación PagoFácil', [
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        if ($response->successful() && $response->json('error') === 0) {
            $token = $response->json('values.accessToken');
            \Log::info('✅ Token obtenido exitosamente', ['token_length' => strlen($token)]);
            return $token;
        }

        \Log::error('❌ Error de autenticación', [
            'status' => $response->status(),
            'response' => $response->body()
        ]);

        throw new Exception('Error al autenticar con PagoFácil: ' . $response->body());
    }


    /**
     * Generar QR para una transacción
     */
    public function generateQr(array $data)
    {
        $token = $this->authenticate();

        $response = Http::withoutVerifying()  // Deshabilitar verificación SSL
            ->timeout(30)
            ->withToken($token)
            ->post("{$this->baseUrl}/generate-qr", $data);

        \Log::info('🎫 Respuesta de generación de QR', [
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        if ($response->successful() && $response->json('error') === 0) {
            return $response->json('values');
        }

        throw new Exception('Error al generar QR: ' . $response->body());
    }


    /**
     * Consultar estado de transacción
     */
    public function consultarTransaccion($transactionId)
    {
        $token = $this->authenticate();

        $response = Http::withToken($token)->post("{$this->baseUrl}/query-transaction", [
            'companyTransactionId' => $transactionId,
        ]);

        if ($response->successful() && $response->json('error') === 0) {
            return $response->json('values');
        }

        throw new Exception('Error al consultar transacción: ' . $response->body());
    }
}
