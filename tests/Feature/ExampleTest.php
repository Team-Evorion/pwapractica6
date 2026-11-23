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
}
