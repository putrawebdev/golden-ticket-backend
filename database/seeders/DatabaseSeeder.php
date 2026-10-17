<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Event Admin',
            'email' => 'admin@goldenticket.com',
            'password' => Hash::make('metland2026'), // Password default
        ]);
    }
}