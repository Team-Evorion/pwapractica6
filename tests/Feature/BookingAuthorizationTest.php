<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_supplier_cannot_view_or_create_bookings(): void
    {
        $supplier = $this->user(Role::SUPPLIER);
        $room = Room::factory()->create();

        $this->actingAs($supplier)->get(route('bookings.index'))->assertForbidden();
        $this->actingAs($supplier)->post(route('bookings.store'), $this->payload($room))->assertForbidden();
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_receptionist_creates_booking_for_selected_customer(): void
    {
        $receptionist = $this->user(Role::RECEPTIONIST);
        $customer = $this->user(Role::CUSTOMER);
        $room = Room::factory()->create();

        $this->actingAs($receptionist)->post(route('bookings.store'), [
            ...$this->payload($room),
            'user_id' => $customer->id,
        ])->assertRedirect(route('bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'user_id' => $customer->id,
            'room_id' => $room->id,
            'status' => Booking::STATUS_PENDING,
        ]);
        $this->assertDatabaseMissing('bookings', ['user_id' => $receptionist->id]);
    }

    public function test_receptionist_confirms_and_customer_cancels_own_booking(): void
    {
        $receptionist = $this->user(Role::RECEPTIONIST);
        $customer = $this->user(Role::CUSTOMER);
        $booking = $this->booking($customer, Room::factory()->create());

        $this->actingAs($receptionist)->patch(route('bookings.status.update', $booking), [
            'status' => Booking::STATUS_CONFIRMED,
        ])->assertRedirect(route('bookings.index'));
        $this->assertSame(Booking::STATUS_CONFIRMED, $booking->refresh()->status);

        $this->actingAs($customer)->patch(route('bookings.status.update', $booking), [
            'status' => Booking::STATUS_CANCELLED,
        ])->assertRedirect(route('bookings.index'));
        $this->assertSame(Booking::STATUS_CANCELLED, $booking->refresh()->status);
    }

    public function test_interval_filter_returns_only_available_operational_rooms(): void
    {
        $customer = $this->user(Role::CUSTOMER);
        $blocked = Room::factory()->create(['room_number' => 2101]);
        $available = Room::factory()->create(['room_number' => 2102]);
        Room::factory()->unavailable()->create(['room_number' => 2103]);
        $this->booking($customer, $blocked);

        $response = $this->actingAs($customer)->get(route('bookings.index', [
            'check_in_date' => now()->addDays(3)->toDateString(),
            'check_out_date' => now()->addDays(4)->toDateString(),
        ]))->assertOk();

        $ids = $response->viewData('availableRooms')->pluck('id');
        $this->assertFalse($ids->contains($blocked->id));
        $this->assertTrue($ids->contains($available->id));
        $this->assertCount(1, $ids);
    }

    public function test_room_with_booking_history_cannot_be_deleted(): void
    {
        $manager = $this->user(Role::HOTEL_MANAGER);
        $customer = $this->user(Role::CUSTOMER);
        $room = Room::factory()->create();
        $this->booking($customer, $room);

        $this->actingAs($manager)
            ->delete(route('rooms.destroy', $room))
            ->assertRedirect(route('rooms.manage'))
            ->assertSessionHasErrors('room');

        $this->assertModelExists($room);
    }

    private function user(string $role): User
    {
        return User::factory()->create([
            'role_id' => Role::where('name', $role)->sole()->id,
            'is_active' => true,
        ]);
    }

    private function booking(User $customer, Room $room): Booking
    {
        return Booking::create([
            'user_id' => $customer->id,
            'room_id' => $room->id,
            'booking_date' => now()->toDateString(),
            'check_in_date' => now()->addDays(2)->toDateString(),
            'check_out_date' => now()->addDays(5)->toDateString(),
            'status' => Booking::STATUS_PENDING,
        ]);
    }

    private function payload(Room $room): array
    {
        return [
            'room_id' => $room->id,
            'check_in_date' => now()->addDays(7)->toDateString(),
            'check_out_date' => now()->addDays(9)->toDateString(),
        ];
    }
}
