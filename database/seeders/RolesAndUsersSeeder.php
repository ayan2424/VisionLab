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
            [
                'name'             => 'Admin User',
                'email'            => 'admin@visioncode.ai',
                'password'         => Hash::make('Admin@12345'),
                'role'             => 'admin',
                'status'           => 'active',
                'student_id'       => null,
                'theme_preference' => 'dark',
                'email_verified_at'=> now(),
            ],
            [
                'name'             => 'Instructor Jane',
                'email'            => 'instructor@visioncode.ai',
                'password'         => Hash::make('Instructor@12345'),
                'role'             => 'instructor',
                'status'           => 'active',
                'student_id'       => null,
                'theme_preference' => 'dark',
                'email_verified_at'=> now(),
            ],
            [
                'name'             => 'Student Alex',
                'email'            => 'student@visioncode.ai',
                'password'         => Hash::make('Student@12345'),
                'role'             => 'student',
                'status'           => 'active',
                'student_id'       => 'STU-DEMO01',
                'theme_preference' => 'dark',
                'email_verified_at'=> now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }

        $this->command->info('✅ Seeded: admin@visioncode.ai | instructor@visioncode.ai | student@visioncode.ai');
        $this->command->info('   Passwords follow the pattern Role@12345 (e.g. Admin@12345)');
    }
}
