<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);

        $rooms = $this->filteredRooms($filters)
            ->where('is_available', true)
            ->orderBy('room_number')
            ->paginate(9)
            ->withQueryString();

        return view('rooms.index', [
            'rooms' => $rooms,
            'roomTypes' => Room::TYPES,
            'filters' => $filters,
        ]);
    }

    public function manage(Request $request): View
    {
        Gate::authorize('viewAny', Room::class);

        $filters = $this->validatedFilters($request);

        $rooms = $this->filteredRooms($filters)
            ->orderBy('room_number')
            ->paginate(12)
            ->withQueryString();

        return view('rooms.manage', [
            'rooms' => $rooms,
            'roomTypes' => Room::TYPES,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Room::class);

        return view('rooms.create', [
            'roomTypes' => Room::TYPES,
        ]);
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        $room = Room::create($request->validated());

        return redirect()
            ->route('rooms.manage')
            ->with('status', "Habitación {$room->room_number} creada correctamente.");
    }

    public function show(Room $room): View
    {
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        Gate::authorize('update', $room);

        return view('rooms.edit', [
            'room' => $room,
            'roomTypes' => Room::TYPES,
        ]);
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $room->update($request->validated());

        return redirect()
            ->route('rooms.manage')
            ->with('status', "Habitación {$room->room_number} actualizada correctamente.");
    }

    public function destroy(Room $room): RedirectResponse
    {
        Gate::authorize('delete', $room);

        $roomNumber = $room->room_number;
        $room->delete();

        return redirect()
            ->route('rooms.manage')
            ->with('status', "Habitación {$roomNumber} eliminada correctamente.");
    }

    /**
     * @param  array{type?: string, min_price?: float, max_price?: float}  $filters
     */
    private function filteredRooms(array $filters): Builder
    {
        return Room::query()
            ->when(
                $filters['type'] ?? null,
                fn (Builder $query, string $type) => $query->where('room_type', $type),
            )
            ->when(
                isset($filters['min_price']),
                fn (Builder $query) => $query->where('room_price', '>=', $filters['min_price']),
            )
            ->when(
                isset($filters['max_price']),
                fn (Builder $query) => $query->where('room_price', '<=', $filters['max_price']),
            );
    }

    /**
     * @return array{type?: string, min_price?: float, max_price?: float}
     */
    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'type' => ['nullable', 'string', 'in:'.implode(',', Room::TYPES)],
            'min_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'gte:min_price'],
        ]);
    }
}
