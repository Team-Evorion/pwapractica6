<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Supply;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RevisionChangesTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_loads_five_roles_and_initial_admin(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('roles', 5);
        $this->assertDatabaseHas('users', [
            'email' => 'admin@hotel.test',
            'is_active' => true,
        ]);

        $admin = User::where('email', 'admin@hotel.test')->first();
        $this->assertNotNull($admin);
        $this->assertSame(Role::ADMINISTRATOR, $admin->role?->name);
    }

    public function test_creating_user_with_unchecked_is_active_persists_inactive_user(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@hotel.test')->firstOrFail();
        $customerRole = Role::where('name', Role::CUSTOMER)->firstOrFail();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Cliente Inactivo',
            'email' => 'inactivo@hotel.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role_id' => $customerRole->id,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'inactivo@hotel.test',
            'is_active' => false,
        ]);
    }

    public function test_login_rate_limiter_blocks_after_five_failed_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'email' => 'noexiste@hotel.test',
                'password' => 'ClaveIncorrecta123!',
            ]);
        }

        $response = $this->post(route('login'), [
            'email' => 'noexiste@hotel.test',
            'password' => 'ClaveIncorrecta123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'Demasiados intentos de inicio de sesion',
            session('errors')->first('email')
        );
    }

    public function test_supplier_can_view_hotel_needs_and_submit_linked_offer(): void
    {
        $this->seed(DatabaseSeeder::class);

        $supplierRole = Role::where('name', Role::SUPPLIER)->firstOrFail();
        $supplier = User::create([
            'name' => 'Proveedor Hotel',
            'email' => 'proveedor@hotel.test',
            'password' => Hash::make('Password123!'),
            'role_id' => $supplierRole->id,
            'is_active' => true,
        ]);

        $need = Supply::where('item_type', 'need')->firstOrFail();

        $dashboardResponse = $this->actingAs($supplier)->get(route('suppliers.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee($need->name);

        $offerResponse = $this->actingAs($supplier)->post(route('suppliers.supplies.store'), [
            'hotel_need_id' => $need->id,
            'name' => 'Propuesta Sabanas Algodon',
            'quantity' => 50,
            'unit_price' => 18.50,
            'notes' => 'Entrega en 48 horas',
        ]);

        $offerResponse->assertRedirect(route('suppliers.dashboard'));
        $this->assertDatabaseHas('supplies', [
            'name' => 'Propuesta Sabanas Algodon',
            'supplier_id' => $supplier->id,
            'hotel_need_id' => $need->id,
            'item_type' => 'offer',
        ]);
    }

    public function test_delete_user_form_uses_data_attribute_and_class_without_inline_onsubmit(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@hotel.test')->firstOrFail();
        $customerRole = Role::where('name', Role::CUSTOMER)->firstOrFail();

        $targetUser = User::create([
            'name' => 'Usuario Objetivo Borrado',
            'email' => 'objetivo.borrado@hotel.test',
            'password' => Hash::make('Password123!'),
            'role_id' => $customerRole->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));
        $response->assertOk();
        $response->assertSee('js-delete-user-form');
        $response->assertSee('data-user-name="'.e($targetUser->name).'"', false);
        $response->assertDontSee('onsubmit="return confirm(', false);
    }

    public function test_supplier_cannot_submit_offer_for_closed_hotel_need(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@hotel.test')->firstOrFail();
        $supplierRole = Role::where('name', Role::SUPPLIER)->firstOrFail();
        $supplier = User::create([
            'name' => 'Proveedor Oferta Cerrada',
            'email' => 'proveedor.cerrada@hotel.test',
            'password' => Hash::make('Password123!'),
            'role_id' => $supplierRole->id,
            'is_active' => true,
        ]);

        $closedNeed = Supply::create([
            'name' => 'Necesidad Cerrada Test',
            'item_type' => 'need',
            'quantity' => 25,
            'supplier_id' => $admin->id,
            'status' => 'closed',
        ]);

        $response = $this->actingAs($supplier)
            ->from(route('suppliers.dashboard'))
            ->post(route('suppliers.supplies.store'), [
                'hotel_need_id' => $closedNeed->id,
                'name' => 'Oferta Invalida Necesidad Cerrada',
                'quantity' => 25,
            ]);

        $response->assertRedirect(route('suppliers.dashboard'));
        $response->assertSessionHasErrors('hotel_need_id');
        $this->assertDatabaseMissing('supplies', [
            'name' => 'Oferta Invalida Necesidad Cerrada',
        ]);
    }

    public function test_deactivated_authenticated_user_cannot_access_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);
        $customerRole = Role::where('name', Role::CUSTOMER)->firstOrFail();

        $user = User::create([
            'name' => 'Usuario Activo Luego Desactivado',
            'email' => 'cambia.estado@hotel.test',
            'password' => Hash::make('Password123!'),
            'role_id' => $customerRole->id,
            'is_active' => true,
        ]);

        $this->actingAs($user)->get('/dashboard')->assertOk();

        $user->update(['is_active' => false]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
