<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
                ]);
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Tu cuenta se encuentra desactivada. Contacta al administrador.',
                ]);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPathFor($user))
            ->with('status', 'Bienvenido de nuevo, ' . $user->name . '.');
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $customerRole = Role::firstOrCreate(['name' => Role::CUSTOMER]);

        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role_id' => $customerRole->id,
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($this->redirectPathFor($user))
            ->with('status', 'Registro exitoso. Bienvenido al Sistema de Reservas de Hotel.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Has cerrado sesión correctamente.');
    }

    protected function redirectPathFor(User $user): string
    {
        $roleName = $user->role?->name;

        return match ($roleName) {
            Role::ADMINISTRATOR => route('admin.dashboard'),
            Role::SUPPLIER => route('suppliers.dashboard'),
            Role::HOTEL_MANAGER, Role::RECEPTIONIST, Role::CUSTOMER => route('dashboard'),
            default => route('dashboard'),
        };
    }
}