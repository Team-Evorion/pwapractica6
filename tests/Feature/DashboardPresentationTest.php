<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\RoomSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPresentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_spanish_role_and_real_summary(): void
    {
        $this->seed(RoleSeeder::class);

        $user = User::factory()->create([
            'role_id' => Role::where('name', Role::CUSTOMER)->value('id'),
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Panel general')
            ->assertSee('Cliente')
            ->assertSee('Habitaciones')
            ->assertSee('Reservaciones activas');
    }

    public function test_every_academic_role_has_a_spanish_display_name(): void
    {
        $this->seed(RoleSeeder::class);

        $this->assertSame([
            'Administrador',
            'Gerente del hotel',
            'Recepcionista',
            'Cliente',
            'Proveedor',
        ], Role::query()->get()->map->display_name->all());
    }

    public function test_premium_room_keeps_a_reasonable_upper_price(): void
    {
        $this->seed(RoomSeeder::class);

        $this->assertSame('145.00', Room::where('room_number', 402)->value('room_price'));
    }
}
