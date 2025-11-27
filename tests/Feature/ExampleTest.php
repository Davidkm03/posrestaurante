<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * La aplicación redirige a login para usuarios no autenticados.
     */
    public function test_the_application_redirects_unauthenticated_users(): void
    {
        $response = $this->get('/');

        // La app requiere autenticación, así que redirige (302) a login
        $response->assertRedirect();
    }
}
