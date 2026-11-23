<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHotelNeedRequest;
use App\Http\Requests\Admin\UpdateHotelNeedStatusRequest;
use App\Models\Supply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SupplyManagementController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Supply::class);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', 'in:need,offer,supply'],
            'status' => ['nullable', 'in:open,closed,offered'],
        ]);

        $supplies = Supply::query()
            ->with(['supplier', 'hotelNeed'])
            ->withCount('offers')
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($nested) use ($search) {
                    $nested
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('supplier', fn ($supplier) => $supplier->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['type'] ?? null, fn ($query, string $type) => $query->where('item_type', $type))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->orderByRaw("CASE WHEN item_type = 'need' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.supplies.index', [
            'supplies' => $supplies,
            'filters' => $filters,
            'summary' => [
                'total' => Supply::count(),
                'open_needs' => Supply::where('item_type', Supply::TYPE_NEED)->where('status', Supply::STATUS_OPEN)->count(),
                'offers' => Supply::where('item_type', Supply::TYPE_OFFER)->count(),
                'suppliers' => Supply::whereNotNull('supplier_id')->where('item_type', '!=', Supply::TYPE_NEED)->distinct('supplier_id')->count('supplier_id'),
            ],
        ]);
    }

    public function storeNeed(StoreHotelNeedRequest $request): RedirectResponse
    {
        Gate::authorize('manageNeeds', Supply::class);

        Supply::create([
            ...$request->validated(),
            'item_type' => Supply::TYPE_NEED,
            'supplier_id' => $request->user()->id,
            'status' => Supply::STATUS_OPEN,
        ]);

        return redirect()
            ->route('admin.supplies.index')
            ->with('status', 'Necesidad del hotel publicada correctamente.');
    }

    public function updateNeedStatus(UpdateHotelNeedStatusRequest $request, Supply $supply): RedirectResponse
    {
        Gate::authorize('manageNeeds', Supply::class);

        abort_unless($supply->item_type === Supply::TYPE_NEED, 404);

        $supply->update(['status' => $request->validated('status')]);

        return back()->with('status', 'Estado de la necesidad actualizado.');
    }
}
