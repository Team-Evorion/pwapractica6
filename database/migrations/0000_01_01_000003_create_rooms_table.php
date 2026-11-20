<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('room_number')->unique();
            $table->string('room_type', 20);
            $table->decimal('room_price', 10, 2);
            $table->boolean('is_available')->default(true);
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('capacity')->default(1);
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
