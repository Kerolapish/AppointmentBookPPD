<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'mkhairulhaf@gmail.com', // You will use this to login
            'password' => Hash::make('password123'), // You will use this password
            'role' => 'super_admin', // This triggers the middleware check
        ]);
    }
}