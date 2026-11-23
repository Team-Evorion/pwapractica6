<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Supply;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSupplyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_open_supply_overview_from_dashboard(): void
    {
        $admin = $this->userWithRole(Role::ADMINISTRATOR);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.supplies.index'), false);

        $this->actingAs($admin)
            ->get(route('admin.supplies.index'))
            ->assertOk()
            ->assertSee('Necesidades y ofertas')
            ->assertSee('Nueva necesidad');
    }

    public function test_administrator_can_publish_and_close_hotel_need(): void
    {
        $admin = $this->userWithRole(Role::ADMINISTRATOR);

        $response = $this->actingAs($admin)->post(route('admin.supplies.needs.store'), [
            'name' => 'Almohadas hipoalergénicas',
            'quantity' => 30,
            'notes' => 'Entrega antes del cierre de mes.',
        ]);

        $response->assertRedirect(route('admin.supplies.index'));

        $need = Supply::where('name', 'Almohadas hipoalergénicas')->firstOrFail();
        $this->assertSame(Supply::TYPE_NEED, $need->item_type);
        $this->assertSame(Supply::STATUS_OPEN, $need->status);
        $this->assertSame($admin->id, $need->supplier_id);

        $this->actingAs($admin)
            ->patch(route('admin.supplies.needs.status', $need), ['status' => Supply::STATUS_CLOSED])
            ->assertRedirect();

        $this->assertDatabaseHas('supplies', [
            'id' => $need->id,
            'status' => Supply::STATUS_CLOSED,
        ]);
    }

    public function test_supplier_cannot_access_admin_supply_management(): void
    {
        $supplier = $this->userWithRole(Role::SUPPLIER);

        $this->actingAs($supplier)
            ->get(route('admin.supplies.index'))
            ->assertForbidden();

        $this->actingAs($supplier)
            ->post(route('admin.supplies.needs.store'), [
                'name' => 'Solicitud no autorizada',
                'quantity' => 10,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('supplies', ['name' => 'Solicitud no autorizada']);
    }

    private function userWithRole(string $roleName): User
    {
        $this->seed(RoleSeeder::class);

        return User::factory()->create([
            'role_id' => Role::where('name', $roleName)->value('id'),
            'is_active' => true,
        ]);
    }
}
