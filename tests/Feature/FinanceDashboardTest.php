<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_booking_creation_preserves_nightly_rate_and_total_amount(): void
    {
        $customer = $this->user(Role::CUSTOMER);
        $room = Room::factory()->create(['room_price' => 125.50]);

        $this->actingAs($customer)->post(route('bookings.store'), [
            'room_id' => $room->id,
            'check_in_date' => now()->addDays(2)->toDateString(),
            'check_out_date' => now()->addDays(5)->toDateString(),
        ])->assertRedirect(route('bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'room_id' => $room->id,
            'nightly_rate' => 125.50,
            'total_amount' => 376.50,
        ]);
    }

    public function test_administrator_and_manager_can_view_finances_while_other_roles_are_forbidden(): void
    {
        $this->actingAs($this->user(Role::ADMINISTRATOR))
            ->get(route('finances.index'))
            ->assertOk()
            ->assertSee('Finanzas del hotel');

        $this->actingAs($this->user(Role::HOTEL_MANAGER))
            ->get(route('finances.index'))
            ->assertOk();

        foreach ([Role::RECEPTIONIST, Role::CUSTOMER, Role::SUPPLIER] as $role) {
            $this->actingAs($this->user($role))
                ->get(route('finances.index'))
                ->assertForbidden();
        }
    }

    public function test_manager_records_partial_payment_and_dashboard_reports_balance(): void
    {
        $manager = $this->user(Role::HOTEL_MANAGER);
        $booking = $this->booking(totalAmount: 300);

        $this->actingAs($manager)->post(route('finances.payments.store', $booking), [
            'kind' => Payment::KIND_PAYMENT,
            'amount' => 120,
            'method' => Payment::METHOD_CARD,
            'reference' => 'POS-1001',
            'paid_at' => now()->toDateString(),
        ])->assertRedirect(route('finances.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'recorded_by' => $manager->id,
            'kind' => Payment::KIND_PAYMENT,
            'amount' => 120,
            'method' => Payment::METHOD_CARD,
            'reference' => 'POS-1001',
        ]);

        $response = $this->actingAs($manager)->get(route('finances.index'))->assertOk();
        $summary = $response->viewData('summary');

        $this->assertSame(300.0, $summary['confirmed_revenue']);
        $this->assertSame(120.0, $summary['net_collected']);
        $this->assertSame(180.0, $summary['pending_balance']);
    }

    public function test_payment_cannot_exceed_balance_and_refund_cannot_exceed_collected_amount(): void
    {
        $administrator = $this->user(Role::ADMINISTRATOR);
        $booking = $this->booking(totalAmount: 200);

        $this->actingAs($administrator)->from(route('finances.index'))
            ->post(route('finances.payments.store', $booking), $this->paymentPayload(250))
            ->assertRedirect(route('finances.index'))
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('payments', 0);

        Payment::factory()->create([
            'booking_id' => $booking->id,
            'recorded_by' => $administrator->id,
            'amount' => 80,
        ]);

        $this->actingAs($administrator)->from(route('finances.index'))
            ->post(route('finances.payments.store', $booking), [
                ...$this->paymentPayload(100),
                'kind' => Payment::KIND_REFUND,
            ])
            ->assertRedirect(route('finances.index'))
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('payments', 1);
    }

    public function test_customer_cannot_record_payment(): void
    {
        $customer = $this->user(Role::CUSTOMER);
        $booking = $this->booking(customer: $customer);

        $this->actingAs($customer)
            ->post(route('finances.payments.store', $booking), $this->paymentPayload(50))
            ->assertForbidden();

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_refund_reduces_collected_amount_and_cancelled_booking_rejects_new_charge(): void
    {
        $manager = $this->user(Role::HOTEL_MANAGER);
        $booking = $this->booking(totalAmount: 200);
        Payment::factory()->create([
            'booking_id' => $booking->id,
            'recorded_by' => $manager->id,
            'amount' => 120,
        ]);
        $booking->update(['status' => Booking::STATUS_CANCELLED]);

        $this->actingAs($manager)->from(route('finances.index'))
            ->post(route('finances.payments.store', $booking), $this->paymentPayload(10))
            ->assertRedirect(route('finances.index'))
            ->assertSessionHasErrors('amount');

        $this->actingAs($manager)->post(route('finances.payments.store', $booking), [
            ...$this->paymentPayload(40),
            'kind' => Payment::KIND_REFUND,
        ])->assertRedirect(route('finances.index'));

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'kind' => Payment::KIND_REFUND,
            'amount' => 40,
        ]);

        $response = $this->actingAs($manager)->get(route('finances.index'))->assertOk();
        $this->assertSame(80.0, $response->viewData('summary')['net_collected']);
    }

    private function user(string $role): User
    {
        return User::factory()->create([
            'role_id' => Role::where('name', $role)->sole()->id,
            'is_active' => true,
        ]);
    }

    private function booking(?User $customer = null, float $totalAmount = 300): Booking
    {
        $customer ??= $this->user(Role::CUSTOMER);

        return Booking::factory()->create([
            'user_id' => $customer->id,
            'status' => Booking::STATUS_CONFIRMED,
            'nightly_rate' => 100,
            'total_amount' => $totalAmount,
        ]);
    }

    /** @return array<string, float|string> */
    private function paymentPayload(float $amount): array
    {
        return [
            'kind' => Payment::KIND_PAYMENT,
            'amount' => $amount,
            'method' => Payment::METHOD_CASH,
            'paid_at' => now()->toDateString(),
        ];
    }
}
