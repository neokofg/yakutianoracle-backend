<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transports', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->timestamps();
            $table->integer('rating');
            $table->boolean('airport_nearby');
            $table->integer('bus_stations');
            $table->boolean('road_nearby');
            $table->foreignUlid('city_id')->constrained('cities')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transports');
    }
};
