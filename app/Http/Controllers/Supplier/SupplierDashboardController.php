<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreSupplyRequest;
use App\Http\Requests\Supplier\UpdateSupplyRequest;
use App\Models\Supply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SupplierDashboardController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Supply::class);

        $userId = (int) $request->user()->id;
        $search = trim((string) $request->query('search', ''));

        $supplies = Supply::where('supplier_id', $userId)
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $totalItems = Supply::where('supplier_id', $userId)->count();
        $totalUnits = (int) Supply::where('supplier_id', $userId)->sum('quantity');

        return view('suppliers.dashboard', compact('supplies', 'search', 'totalItems', 'totalUnits'));
    }

    public function store(StoreSupplyRequest $request): RedirectResponse
    {
        Gate::authorize('create', Supply::class);

        $data = $request->validated();

        Supply::create([
            'name' => $data['name'],
            'quantity' => $data['quantity'],
            'supplier_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('suppliers.dashboard')
            ->with('status', 'Suministro u oferta registrado correctamente.');
    }

    public function edit(Supply $supply): View
    {
        Gate::authorize('update', $supply);

        return view('suppliers.edit', compact('supply'));
    }

    public function update(UpdateSupplyRequest $request, Supply $supply): RedirectResponse
    {
        Gate::authorize('update', $supply);

        $data = $request->validated();

        $supply->update([
            'name' => $data['name'],
            'quantity' => $data['quantity'],
        ]);

        return redirect()
            ->route('suppliers.dashboard')
            ->with('status', 'Registro actualizado correctamente.');
    }

    public function destroy(Supply $supply): RedirectResponse
    {
        Gate::authorize('delete', $supply);

        $supply->delete();

        return redirect()
            ->route('suppliers.dashboard')
            ->with('status', 'Registro eliminado de tu catálogo.');
    }
}