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
        Schema::create('email_tokens', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->timestamps();
            $table->string('token');
            $table->ipAddress('ipv4');
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_tokens');
    }
};
