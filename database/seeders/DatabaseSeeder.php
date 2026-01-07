<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Appointment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the Admin User
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        // 2. Create Sample Appointments (Using NEW columns: purpose, ips, etc.)
        
        Appointment::create([
            'user_id' => $user->id,
            'name' => 'Admin User',
            'ips' => 'Sekolah Sri Johor',     // <--- New Column
            'purpose' => 'Budget Consultation', // <--- New Column (was 'title')
            'location' => 'Bilik Mesyuarat 1',
            'date' => '2026-01-20',
            'time' => '09:00:00',
            'status' => 'confirmed',
        ]);

        Appointment::create([
            'user_id' => $user->id,
            'name' => 'Admin User',
            'ips' => 'High School Kluang',
            'purpose' => 'Submit Documents',
            'location' => 'Kaunter Utama',
            'date' => '2026-01-22',
            'time' => '11:30:00',
            'status' => 'pending',
        ]);
    }
}