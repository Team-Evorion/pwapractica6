<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_active_user_can_log_in_and_log_out(): void
    {
        $user = $this->userWithPassword('hotel-secret');

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'hotel-secret',
        ])->assertRedirect(route('rooms.index'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('rooms.index'))
            ->assertSessionHas('status');

        $this->assertGuest();
    }

    public function test_invalid_credentials_and_inactive_users_cannot_log_in(): void
    {
        $activeUser = $this->userWithPassword('correct-password');
        $inactiveUser = $this->userWithPassword('correct-password', false);

        $this->post(route('login.store'), [
            'email' => $activeUser->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->post(route('login.store'), [
            'email' => $inactiveUser->email,
            'password' => 'correct-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    private function userWithPassword(string $password, bool $isActive = true): User
    {
        $role = Role::firstOrCreate(['name' => Role::CUSTOMER]);

        return User::factory()->create([
            'role_id' => $role->id,
            'password' => Hash::make($password),
            'is_active' => $isActive,
        ]);
    }
}
