<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_routes_require_authentication(): void
    {
        $room = Room::factory()->create();

        $this->get(route('bookings.index'))->assertRedirect(route('login'));
        $this->post(route('bookings.store'), $this->bookingPayload($room))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_creates_a_pending_booking_without_accepting_user_id(): void
    {
        $user = $this->customer();
        $otherUser = $this->customer();
        $room = Room::factory()->create();

        $payload = $this->bookingPayload($room);
        $payload['user_id'] = $otherUser->id;

        $this->actingAs($user)
            ->post(route('bookings.store'), $payload)
            ->assertRedirect(route('bookings.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'room_id' => $room->id,
            'status' => Booking::STATUS_PENDING,
        ]);

        $this->assertDatabaseMissing('bookings', [
            'user_id' => $otherUser->id,
            'room_id' => $room->id,
        ]);
    }

    public function test_overlapping_active_booking_is_rejected(): void
    {
        $user = $this->customer();
        $room = Room::factory()->create();
        $initialBookingCount = Booking::count();

        Booking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'booking_date' => now()->toDateString(),
            'check_in_date' => now()->addDays(3)->toDateString(),
            'check_out_date' => now()->addDays(6)->toDateString(),
            'status' => Booking::STATUS_CONFIRMED,
        ]);

        $this->actingAs($user)
            ->from(route('rooms.show', $room))
            ->post(route('bookings.store'), [
                'room_id' => $room->id,
                'check_in_date' => now()->addDays(5)->toDateString(),
                'check_out_date' => now()->addDays(7)->toDateString(),
            ])
            ->assertRedirect(route('rooms.show', $room))
            ->assertSessionHasErrors('check_in_date');

        $this->assertDatabaseCount('bookings', $initialBookingCount + 1);
    }

    public function test_user_only_sees_their_own_bookings(): void
    {
        $user = $this->customer();
        $otherUser = $this->customer();
        $ownRoom = Room::factory()->create(['room_number' => 1201]);
        $otherRoom = Room::factory()->create(['room_number' => 1202]);

        $this->createBooking($user, $ownRoom);
        $this->createBooking($otherUser, $otherRoom);

        $response = $this->actingAs($user)
            ->get(route('bookings.index'))
            ->assertOk();

        $roomIds = $response->viewData('bookings')->pluck('room_id');
        $this->assertTrue($roomIds->contains($ownRoom->id));
        $this->assertFalse($roomIds->contains($otherRoom->id));
    }

    private function customer(): User
    {
        $role = Role::firstOrCreate(['name' => Role::CUSTOMER]);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    /** @return array<string, int|string> */
    private function bookingPayload(Room $room): array
    {
        return [
            'room_id' => $room->id,
            'check_in_date' => now()->addDays(2)->toDateString(),
            'check_out_date' => now()->addDays(4)->toDateString(),
        ];
    }

    private function createBooking(User $user, Room $room): Booking
    {
        return Booking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'booking_date' => now()->toDateString(),
            'check_in_date' => now()->addDays(2)->toDateString(),
            'check_out_date' => now()->addDays(4)->toDateString(),
            'status' => Booking::STATUS_PENDING,
        ]);
    }
}
