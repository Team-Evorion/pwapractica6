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

        $hotelNeeds = Supply::where('item_type', 'need')
            ->where('status', 'open')
            ->withCount('offers')
            ->orderByDesc('created_at')
            ->get();

        $supplies = Supply::where('supplier_id', $userId)
            ->where('item_type', '!=', 'need')
            ->with('hotelNeed')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $totalItems = Supply::where('supplier_id', $userId)->where('item_type', '!=', 'need')->count();
        $totalUnits = (int) Supply::where('supplier_id', $userId)->where('item_type', '!=', 'need')->sum('quantity');

        return view('suppliers.dashboard', compact(
            'supplies',
            'hotelNeeds',
            'search',
            'totalItems',
            'totalUnits'
        ));
    }

    public function store(StoreSupplyRequest $request): RedirectResponse
    {
        Gate::authorize('create', Supply::class);

        $data = $request->validated();
        $need = ! empty($data['hotel_need_id'])
            ? Supply::where('id', $data['hotel_need_id'])->where('item_type', 'need')->first()
            : null;

        Supply::create([
            'name' => $data['name'],
            'item_type' => $need ? 'offer' : 'supply',
            'quantity' => $data['quantity'],
            'supplier_id' => $request->user()->id,
            'hotel_need_id' => $need?->id,
            'unit_price' => $data['unit_price'] ?? null,
            'status' => $need ? 'offered' : 'open',
            'notes' => $data['notes'] ?? null,
        ]);

        $message = $need
            ? "Oferta vinculada a la necesidad \"{$need->name}\" enviada correctamente."
            : 'Suministro u oferta registrado correctamente.';

        return redirect()
            ->route('suppliers.dashboard')
            ->with('status', $message);
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
            ->with('status', 'Registro eliminado de tu catalogo.');
    }
}
