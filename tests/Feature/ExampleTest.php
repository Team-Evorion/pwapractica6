<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_displays_the_public_hotel_homepage(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Donde cada estancia se siente extraordinaria')
            ->assertSee('Descubrir habitaciones');
    }

    public function test_web_responses_include_basic_security_headers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_registration_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('register.store'), [])->assertRedirect();
        }

        $this->post(route('register.store'), [])->assertTooManyRequests();
    }
}
