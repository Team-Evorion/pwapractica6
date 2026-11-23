<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

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
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $data['image_path'] = 'storage/'.$request->file('image')->store('rooms', 'public');
        }

        $room = Room::create($data);

        return redirect()
            ->route('rooms.manage')
            ->with('status', "Habitación {$room->room_number} creada correctamente.");
    }

    public function show(Room $room): View
    {
        if (! $room->is_available && ! Gate::allows('update', $room)) {
            abort(404);
        }

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
        $data = $request->safe()->except('image');
        $previousImagePath = $room->image_path;
        $newStoredPath = null;

        if ($request->hasFile('image')) {
            $newStoredPath = $request->file('image')->store('rooms', 'public');
            $data['image_path'] = 'storage/'.$newStoredPath;
        }

        try {
            $room->update($data);
        } catch (Throwable $exception) {
            if ($newStoredPath !== null) {
                Storage::disk('public')->delete($newStoredPath);
            }

            throw $exception;
        }

        if ($newStoredPath !== null) {
            $this->deleteUploadedImage($previousImagePath);
        }

        return redirect()
            ->route('rooms.manage')
            ->with('status', "Habitación {$room->room_number} actualizada correctamente.");
    }

    public function destroy(Room $room): RedirectResponse
    {
        Gate::authorize('delete', $room);

        if ($room->bookings()->exists()) {
            return redirect()
                ->route('rooms.manage')
                ->withErrors([
                    'room' => 'No se puede eliminar una habitación con historial de reservaciones. Desactívala para conservar la información.',
                ]);
        }

        $roomNumber = $room->room_number;
        $imagePath = $room->image_path;
        $room->delete();
        $this->deleteUploadedImage($imagePath);

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

    private function deleteUploadedImage(?string $imagePath): void
    {
        if ($imagePath === null || ! str_starts_with($imagePath, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(substr($imagePath, strlen('storage/')));
    }
}
