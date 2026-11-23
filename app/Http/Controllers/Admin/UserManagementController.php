<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $search = trim((string) $request->query('search', ''));
        $roleFilter = $request->query('role_id');
        $statusFilter = $request->query('status');

        $users = User::with('role')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($roleFilter, fn ($query) => $query->where('role_id', $roleFilter))
            ->when($statusFilter === 'active', fn ($query) => $query->where('is_active', true))
            ->when($statusFilter === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles', 'search', 'roleFilter', 'statusFilter'));
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $data = $request->validated();

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role_id' => $data['role_id'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuario creado y rol asignado correctamente.');
    }

    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $data = $request->validated();
        $isActive = $request->boolean('is_active');

        if ((int) $request->user()->id === (int) $user->id && ! $isActive) {
            return back()
                ->withInput()
                ->withErrors(['is_active' => 'No puedes desactivar tu propia cuenta de administrador.']);
        }

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role_id'],
            'is_active' => $isActive,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $user->update($payload);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Usuario {$user->name} actualizado correctamente.");
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('toggleActive', $user);

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        $estado = $user->is_active ? 'activado' : 'desactivado';

        return redirect()
            ->route('admin.users.index')
            ->with('status', "El usuario {$user->name} ha sido {$estado}.");
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        try {
            $nombre = $user->name;
            $user->delete();

            return redirect()
                ->route('admin.users.index')
                ->with('status', "Usuario {$nombre} eliminado del sistema.");
        } catch (QueryException $e) {
            $user->update(['is_active' => false]);

            return redirect()
                ->route('admin.users.index')
                ->with('status', "El usuario {$user->name} tiene registros asociados y fue desactivado para preservar la integridad.");
        }
    }
}
