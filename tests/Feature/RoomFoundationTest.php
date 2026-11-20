<?php

namespace Tests\Feature;

use App\Models\Room;
use Database\Seeders\RoomSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RoomFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rooms_table_contains_the_academic_and_catalog_fields(): void
    {
        $this->assertTrue(Schema::hasColumns('rooms', [
            'id',
            'room_number',
            'room_type',
            'room_price',
            'is_available',
            'description',
            'capacity',
            'image_path',
            'created_at',
            'updated_at',
        ]));
    }

    public function test_room_uses_database_defaults_and_model_casts(): void
    {
        $room = Room::create([
            'room_number' => 501,
            'room_type' => 'Doble',
            'room_price' => '89.50',
        ])->refresh();

        $this->assertSame(501, $room->room_number);
        $this->assertSame('89.50', $room->room_price);
        $this->assertTrue($room->is_available);
        $this->assertSame(1, $room->capacity);
        $this->assertNull($room->description);
        $this->assertNull($room->image_path);
    }

    public function test_room_factory_supports_available_and_unavailable_rooms(): void
    {
        $available = Room::factory()->create();
        $unavailable = Room::factory()->unavailable()->create();

        $this->assertTrue($available->is_available);
        $this->assertFalse($unavailable->is_available);
        $this->assertGreaterThanOrEqual(1, $available->capacity);
        $this->assertGreaterThanOrEqual(0, (float) $available->room_price);
    }

    public function test_room_seeder_is_repeatable(): void
    {
        $this->seed(RoomSeeder::class);
        $this->seed(RoomSeeder::class);

        $this->assertDatabaseCount('rooms', 8);
        $this->assertDatabaseHas('rooms', [
            'room_number' => 301,
            'room_type' => 'Suite',
            'room_price' => '140.00',
            'is_available' => true,
        ]);
    }
}
