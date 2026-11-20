<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoomManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_catalog_only_shows_operational_rooms(): void
    {
        $available = Room::factory()->create([
            'room_number' => 701,
            'room_type' => 'Suite',
        ]);
        $unavailable = Room::factory()->unavailable()->create([
            'room_number' => 702,
            'room_type' => 'Familiar',
        ]);

        $this->get(route('rooms.index'))
            ->assertOk()
            ->assertSee((string) $available->room_number)
            ->assertDontSee((string) $unavailable->room_number);

        $this->get(route('rooms.show', $available))
            ->assertOk()
            ->assertSee('Habitación '.$available->room_number);
    }

    public function test_catalog_filters_by_type_and_price_range(): void
    {
        Room::factory()->create([
            'room_number' => 711,
            'room_type' => 'Doble',
            'room_price' => '75.00',
        ]);
        Room::factory()->create([
            'room_number' => 712,
            'room_type' => 'Suite',
            'room_price' => '160.00',
        ]);
        Room::factory()->create([
            'room_number' => 713,
            'room_type' => 'Suite',
            'room_price' => '240.00',
        ]);

        $this->get(route('rooms.index', [
            'type' => 'Suite',
            'min_price' => 100,
            'max_price' => 200,
        ]))
            ->assertOk()
            ->assertSee('712')
            ->assertDontSee('711')
            ->assertDontSee('713');
    }

    public function test_customer_cannot_access_room_management(): void
    {
        $customer = $this->userWithRole(Role::CUSTOMER);
        $room = Room::factory()->create();

        $this->actingAs($customer)
            ->get(route('rooms.manage'))
            ->assertForbidden();

        $this->actingAs($customer)
            ->get(route('rooms.edit', $room))
            ->assertForbidden();
    }

    public function test_only_room_managers_can_open_an_unavailable_room(): void
    {
        $room = Room::factory()->unavailable()->create();

        $this->get(route('rooms.show', $room))
            ->assertNotFound();

        $customer = $this->userWithRole(Role::CUSTOMER);
        $this->actingAs($customer)
            ->get(route('rooms.show', $room))
            ->assertNotFound();

        $manager = $this->userWithRole(Role::HOTEL_MANAGER);
        $this->actingAs($manager)
            ->get(route('rooms.show', $room))
            ->assertOk();
    }

    public function test_hotel_manager_can_create_update_and_delete_a_room(): void
    {
        $manager = $this->userWithRole(Role::HOTEL_MANAGER);

        $this->actingAs($manager)
            ->get(route('rooms.create'))
            ->assertOk();

        $this->post(route('rooms.store'), $this->validPayload([
            'room_number' => 801,
        ]))->assertRedirectToRoute('rooms.manage');

        $room = Room::where('room_number', 801)->sole();

        $this->get(route('rooms.manage'))
            ->assertOk()
            ->assertSee('801');

        $this->put(route('rooms.update', $room), $this->validPayload([
            'room_number' => 801,
            'room_type' => 'Suite',
            'room_price' => '180.00',
            'is_available' => '0',
            'capacity' => 3,
        ]))->assertRedirectToRoute('rooms.manage');

        $room->refresh();

        $this->assertSame('Suite', $room->room_type);
        $this->assertSame('180.00', $room->room_price);
        $this->assertFalse($room->is_available);
        $this->assertSame(3, $room->capacity);

        $this->delete(route('rooms.destroy', $room))
            ->assertRedirectToRoute('rooms.manage');

        $this->assertModelMissing($room);
    }

    public function test_administrator_can_manage_rooms(): void
    {
        $administrator = $this->userWithRole(Role::ADMINISTRATOR);

        $this->actingAs($administrator)
            ->post(route('rooms.store'), $this->validPayload([
                'room_number' => 901,
            ]))
            ->assertRedirectToRoute('rooms.manage');

        $this->assertDatabaseHas('rooms', [
            'room_number' => 901,
            'room_type' => 'Doble',
        ]);
    }

    public function test_room_validation_rejects_duplicates_and_invalid_values(): void
    {
        $manager = $this->userWithRole(Role::HOTEL_MANAGER);
        Room::factory()->create(['room_number' => 1001]);

        $this->actingAs($manager)
            ->post(route('rooms.store'), $this->validPayload([
                'room_number' => 1001,
                'room_type' => 'Penthouse',
                'room_price' => '-1',
                'capacity' => 0,
            ]))
            ->assertSessionHasErrors([
                'room_number',
                'room_type',
                'room_price',
                'capacity',
            ]);
    }

    public function test_price_filter_rejects_an_inverted_range(): void
    {
        $this->get(route('rooms.index', [
            'min_price' => 200,
            'max_price' => 100,
        ]))->assertSessionHasErrors('max_price');
    }

    public function test_hotel_manager_can_upload_replace_and_delete_a_room_image(): void
    {
        Storage::fake('public');

        $manager = $this->userWithRole(Role::HOTEL_MANAGER);

        $this->actingAs($manager)
            ->post(route('rooms.store'), $this->validPayload([
                'room_number' => 1101,
                'image' => UploadedFile::fake()->image('first-room.jpg', 1200, 800),
            ]))
            ->assertRedirectToRoute('rooms.manage');

        $room = Room::where('room_number', 1101)->sole();
        $firstPath = substr($room->image_path, strlen('storage/'));
        Storage::disk('public')->assertExists($firstPath);

        $this->put(route('rooms.update', $room), $this->validPayload([
            'room_number' => 1101,
            'image' => UploadedFile::fake()->image('replacement-room.png', 1200, 800),
        ]))->assertRedirectToRoute('rooms.manage');

        $room->refresh();
        $replacementPath = substr($room->image_path, strlen('storage/'));
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($replacementPath);

        $this->delete(route('rooms.destroy', $room))
            ->assertRedirectToRoute('rooms.manage');

        Storage::disk('public')->assertMissing($replacementPath);
    }

    private function userWithRole(string $roleName): User
    {
        return User::factory()->create([
            'role_id' => Role::where('name', $roleName)->sole()->id,
            'is_active' => true,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_replace([
            'room_number' => 800,
            'room_type' => 'Doble',
            'room_price' => '95.50',
            'is_available' => '1',
            'description' => 'Habitación cómoda para una estancia tranquila.',
            'capacity' => 2,
        ], $overrides);
    }
}
