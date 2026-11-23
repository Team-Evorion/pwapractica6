<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Valida en servidor que el usuario esté autenticado, activo y posea al menos uno de los roles indicados.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Debes iniciar sesión para continuar.']);
        }

        $user = Auth::user();
        $user->loadMissing('role');

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta se encuentra desactivada.']);
        }

        $currentRole = $user->role?->name;

        if ($currentRole === null || ! in_array($currentRole, $roles, true)) {
            abort(403, 'No tienes permisos suficientes para acceder a este recurso.');
        }

        return $next($request);
    }
}