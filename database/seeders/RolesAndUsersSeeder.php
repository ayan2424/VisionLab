<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // ── Admins ──
            [
                'name' => 'Admin User',
                'email' => 'admin@VisionLab.ai',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
                'status' => 'active',
                'theme_preference' => 'dark',
                'email_verified_at' => now(),
            ],

            // ── Instructors ──
            [
                'name' => 'Instructor Jane',
                'email' => 'instructor@VisionLab.ai',
                'password' => Hash::make('Instructor@12345'),
                'role' => 'instructor',
                'status' => 'active',
                'theme_preference' => 'dark',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Professor Khan',
                'email' => 'khan@VisionLab.ai',
                'password' => Hash::make('Instructor@12345'),
                'role' => 'instructor',
                'status' => 'active',
                'theme_preference' => 'dark',
                'email_verified_at' => now(),
            ],

            // ── Students ──
            [
                'name' => 'Student Alex',
                'email' => 'student@VisionLab.ai',
                'password' => Hash::make('Student@12345'),
                'role' => 'student',
                'status' => 'active',
                'theme_preference' => 'dark',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sarah Miller',
                'email' => 'sarah@VisionLab.ai',
                'password' => Hash::make('Student@12345'),
                'role' => 'student',
                'status' => 'active',
                'theme_preference' => 'dark',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ahmed Raza',
                'email' => 'ahmed@VisionLab.ai',
                'password' => Hash::make('Student@12345'),
                'role' => 'student',
                'status' => 'active',
                'theme_preference' => 'dark',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Emma Chen',
                'email' => 'emma@VisionLab.ai',
                'password' => Hash::make('Student@12345'),
                'role' => 'student',
                'status' => 'active',
                'theme_preference' => 'dark',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'David Park',
                'email' => 'david@VisionLab.ai',
                'password' => Hash::make('Student@12345'),
                'role' => 'student',
                'status' => 'active',
                'theme_preference' => 'dark',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }

        $this->command->info('✅ Seeded 8 users: 1 admin, 2 instructors, 5 students');
        $this->command->info('   Logins: admin@VisionLab.ai / Admin@12345');
        $this->command->info('   Logins: instructor@VisionLab.ai / Instructor@12345');
        $this->command->info('   Logins: student@VisionLab.ai / Student@12345');
    }
}
