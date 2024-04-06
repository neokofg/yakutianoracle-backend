<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MStaack\LaravelPostgis\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->timestamps();
            $table->integer('fid')->unique();
            $table->string('title');
            $table->string('place');
            $table->integer('population');
            $table->integer('year_round_rating')->nullable();
            $table->point('geometry', 'geography',4326);
            $table->foreignUlid('region_id')->constrained('regions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
