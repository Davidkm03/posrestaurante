<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Factus API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la integración con Factus - Facturación Electrónica DIAN
    | Documentación: https://docs.factus.com.co
    |
    */

    // URL base de la API (sandbox o producción)
    'base_url' => env('FACTUS_BASE_URL', 'https://api-sandbox.factus.com.co'),

    // Credenciales OAuth2
    'client_id' => env('FACTUS_CLIENT_ID'),
    'client_secret' => env('FACTUS_CLIENT_SECRET'),

    // Credenciales de usuario Factus
    'username' => env('FACTUS_USERNAME'),
    'password' => env('FACTUS_PASSWORD'),

    // Configuración adicional
    'timeout' => env('FACTUS_TIMEOUT', 30),
    
    // Ruta donde se guarda el token
    'token_path' => 'factus/token.json',
];
