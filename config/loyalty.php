<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Programa de Lealtad - Configuración
    |--------------------------------------------------------------------------
    */

    // Puntos por cada X pesos gastados
    'points_per_amount' => env('LOYALTY_POINTS_PER_AMOUNT', 1),
    'amount_for_points' => env('LOYALTY_AMOUNT_FOR_POINTS', 1000),

    // Valor de cada punto en pesos (para canje)
    'point_value' => env('LOYALTY_POINT_VALUE', 100),

    // Mínimo de puntos para poder canjear
    'min_points_redeem' => env('LOYALTY_MIN_REDEEM', 100),

    // Días para que expiren los puntos (0 = nunca expiran)
    'expiration_days' => env('LOYALTY_EXPIRATION_DAYS', 365),

    // Bonus de cumpleaños
    'birthday_bonus' => env('LOYALTY_BIRTHDAY_BONUS', 50),

    // Puntos por referido
    'referral_points' => env('LOYALTY_REFERRAL_POINTS', 100),

    // Niveles del programa
    'levels' => [
        'bronze' => [
            'min_points' => 0,
            'multiplier' => 1.0,
            'color' => '#CD7F32',
            'benefits' => [],
            'description' => 'Nivel inicial del programa',
        ],
        'silver' => [
            'min_points' => 500,
            'multiplier' => 1.2,
            'color' => '#C0C0C0',
            'benefits' => ['birthday_bonus'],
            'description' => 'Bonus de cumpleaños y 20% extra en puntos',
        ],
        'gold' => [
            'min_points' => 2000,
            'multiplier' => 1.5,
            'color' => '#FFD700',
            'benefits' => ['birthday_bonus', 'priority_reservation'],
            'description' => '50% extra en puntos y reservas prioritarias',
        ],
        'platinum' => [
            'min_points' => 5000,
            'multiplier' => 2.0,
            'color' => '#E5E4E2',
            'benefits' => ['birthday_bonus', 'priority_reservation', 'exclusive_events'],
            'description' => 'Doble de puntos y acceso a eventos exclusivos',
        ],
    ],
];
