<?php

namespace App\Services\Factus;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FactusAuthService
{
    private string $tokenPath;

    public function __construct()
    {
        $this->tokenPath = config('factus.token_path', 'factus/token.json');
    }

    /**
     * Obtener token válido (automáticamente refresca si es necesario)
     */
    public function getToken(): string
    {
        // Si no existe el archivo de token, autenticar
        if (!Storage::exists($this->tokenPath)) {
            return $this->authenticate();
        }

        $tokenData = json_decode(Storage::get($this->tokenPath), true);

        // Si el token está expirado, refrescar
        if ($this->isExpired($tokenData)) {
            return $this->refreshToken($tokenData);
        }

        return $tokenData['access_token'];
    }

    /**
     * Verificar si el token está expirado
     */
    private function isExpired(array $tokenData): bool
    {
        // Agregar un margen de 60 segundos para evitar problemas de timing
        return time() > ($tokenData['created_at'] + $tokenData['expires_in'] - 60);
    }

    /**
     * Autenticar con usuario y contraseña (grant_type: password)
     */
    public function authenticate(): string
    {
        Log::info('Factus: Autenticando con credenciales...');

        $response = Http::timeout(config('factus.timeout', 30))
            ->asForm()
            ->post(config('factus.base_url') . '/oauth/token', [
                'grant_type' => 'password',
                'client_id' => config('factus.client_id'),
                'client_secret' => config('factus.client_secret'),
                'username' => config('factus.username'),
                'password' => config('factus.password'),
            ]);

        if (!$response->successful()) {
            Log::error('Factus: Error de autenticación', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception('Error de autenticación con Factus: ' . $response->body());
        }

        $data = $response->json();
        $data['created_at'] = time();

        // Guardar token en storage
        Storage::put($this->tokenPath, json_encode($data, JSON_PRETTY_PRINT));

        Log::info('Factus: Autenticación exitosa');

        return $data['access_token'];
    }

    /**
     * Refrescar token usando refresh_token
     */
    public function refreshToken(array $tokenData): string
    {
        Log::info('Factus: Refrescando token...');

        // Si no hay refresh_token, autenticar desde cero
        if (empty($tokenData['refresh_token'])) {
            return $this->authenticate();
        }

        $response = Http::timeout(config('factus.timeout', 30))
            ->asForm()
            ->post(config('factus.base_url') . '/oauth/token', [
                'grant_type' => 'refresh_token',
                'client_id' => config('factus.client_id'),
                'client_secret' => config('factus.client_secret'),
                'refresh_token' => $tokenData['refresh_token'],
            ]);

        // Si el refresh falla, intentar autenticar desde cero
        if (!$response->successful()) {
            Log::warning('Factus: Refresh token falló, reautenticando...', [
                'status' => $response->status()
            ]);
            return $this->authenticate();
        }

        $data = $response->json();
        $data['created_at'] = time();

        Storage::put($this->tokenPath, json_encode($data, JSON_PRETTY_PRINT));

        Log::info('Factus: Token refrescado exitosamente');

        return $data['access_token'];
    }

    /**
     * Invalidar token actual (logout)
     */
    public function logout(): void
    {
        if (Storage::exists($this->tokenPath)) {
            Storage::delete($this->tokenPath);
        }
        Log::info('Factus: Sesión cerrada');
    }

    /**
     * Verificar si hay un token válido
     */
    public function hasValidToken(): bool
    {
        if (!Storage::exists($this->tokenPath)) {
            return false;
        }

        $tokenData = json_decode(Storage::get($this->tokenPath), true);
        return !$this->isExpired($tokenData);
    }

    /**
     * Obtener información del token actual
     */
    public function getTokenInfo(): ?array
    {
        if (!Storage::exists($this->tokenPath)) {
            return null;
        }

        $tokenData = json_decode(Storage::get($this->tokenPath), true);
        
        return [
            'valid' => !$this->isExpired($tokenData),
            'created_at' => date('Y-m-d H:i:s', $tokenData['created_at'] ?? 0),
            'expires_at' => date('Y-m-d H:i:s', ($tokenData['created_at'] ?? 0) + ($tokenData['expires_in'] ?? 0)),
            'expires_in_seconds' => max(0, ($tokenData['created_at'] ?? 0) + ($tokenData['expires_in'] ?? 0) - time()),
        ];
    }
}
