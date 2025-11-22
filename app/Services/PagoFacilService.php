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
        $response = Http::post("{$this->baseUrl}/login", [
            'tcTokenService' => $this->tokenService,
            'tcTokenSecret' => $this->tokenSecret,
        ]);

        if ($response->successful() && $response->json('error') === 0) {
            return $response->json('values.accessToken'); // Ajustado según la doc: values -> accessToken
        }

        throw new Exception('Error al autenticar con PagoFácil: ' . $response->body());
    }

    /**
     * Generar QR para una transacción
     */
    public function generateQr(array $data)
    {
        $token = $this->authenticate();

        $response = Http::withToken($token)->post("{$this->baseUrl}/generate-qr", $data);

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
