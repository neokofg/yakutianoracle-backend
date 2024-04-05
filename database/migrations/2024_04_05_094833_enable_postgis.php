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
        if (env('DB_CONNECTION') === 'pgsql') {
            Schema::enablePostgisIfNotExists();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (env('DB_CONNECTION') === 'pgsql') {
            Schema::disablePostgisIfExists();
        }
    }
};
