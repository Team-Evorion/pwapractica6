<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('nightly_rate', 10, 2)->nullable()->after('status');
            $table->decimal('total_amount', 10, 2)->nullable()->after('nightly_rate');
            $table->index(['status', 'check_in_date'], 'bookings_status_check_in_index');
        });

        DB::table('bookings')
            ->join('rooms', 'rooms.id', '=', 'bookings.room_id')
            ->select('bookings.id', 'bookings.check_in_date', 'bookings.check_out_date', 'rooms.room_price')
            ->orderBy('bookings.id')
            ->chunkById(200, function ($bookings): void {
                foreach ($bookings as $booking) {
                    $nights = max(1, Carbon::parse($booking->check_in_date)->diffInDays(Carbon::parse($booking->check_out_date)));
                    $nightlyRate = (float) $booking->room_price;

                    DB::table('bookings')->where('id', $booking->id)->update([
                        'nightly_rate' => $nightlyRate,
                        'total_amount' => $nightlyRate * $nights,
                    ]);
                }
            }, 'bookings.id', 'id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_status_check_in_index');
            $table->dropColumn(['nightly_rate', 'total_amount']);
        });
    }
};
