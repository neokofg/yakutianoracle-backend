<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\TrustedIp;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

         $user = \App\Models\User::factory()->create([
             'name' => 'Admin',
         ]);

         TrustedIp::create([
             'user_id' => $user->id,
             'ipv4' => '127.0.0.1'
         ]);
    }
}
