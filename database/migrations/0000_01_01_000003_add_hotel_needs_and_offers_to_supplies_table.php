<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplies', function (Blueprint $table) {
            $table->string('item_type', 20)->default('offer')->after('name');$table->foreignId('hotel_need_id')
                ->nullable()
                ->after('supplier_id')
                ->constrained('supplies')
                ->nullOnDelete();
            $table->decimal('unit_price', 10, 2)->nullable()->after('quantity');
            $table->string('status', 30)->default('open')->after('unit_price');$table->string('notes', 500)->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('supplies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hotel_need_id');$table->dropColumn(['item_type', 'unit_price', 'status', 'notes']);
        });
    }
};