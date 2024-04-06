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
        Schema::create('regions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->timestamps();
            $table->integer('fid')->unique();
            $table->string('title');
            $table->multipolygon('geometry', 'geometry',3857);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
