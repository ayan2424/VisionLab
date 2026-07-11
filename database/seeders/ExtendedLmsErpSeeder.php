<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\FeeChallan;
use App\Models\Employee;
use App\Models\LibraryBook;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ExtendedLmsErpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@visioncode.ai')->first();
        $instructor = User::where('email', 'instructor@visioncode.ai')->first();
        $student = User::where('email', 'student@visioncode.ai')->first();
        $course = Course::first();

        if (!$admin || !$instructor || !$student || !$course) {
            return;
        }

        // --- LMS SEEDING ---
        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Module 1: Introduction to AI',
            'description' => 'Learn the basics of Artificial Intelligence.',
            'order_index' => 1
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Module 2: Neural Networks',
            'description' => 'Deep dive into deep learning.',
            'order_index' => 2
        ]);

        Lesson::create([
            'course_module_id' => $module1->id,
            'title' => 'What is AI?',
            'type' => 'video',
            'content_url' => 'https://www.youtube.com/watch?v=JMUxmLyrhSk',
            'duration_minutes' => 15,
            'order_index' => 1
        ]);

        Lesson::create([
            'course_module_id' => $module1->id,
            'title' => 'AI History PDF',
            'type' => 'pdf',
            'content_url' => 'https://example.com/ai_history.pdf',
            'duration_minutes' => 30,
            'order_index' => 2
        ]);

        // --- ERP SEEDING ---
        FeeChallan::create([
            'user_id' => $student->id,
            'challan_number' => 'CHL-' . strtoupper(uniqid()),
            'amount' => 50000.00,
            'late_fee' => 0.00,
            'due_date' => Carbon::now()->addDays(10),
            'status' => 'pending'
        ]);

        FeeChallan::create([
            'user_id' => $student->id,
            'challan_number' => 'CHL-' . strtoupper(uniqid()),
            'amount' => 45000.00,
            'late_fee' => 1000.00,
            'due_date' => Carbon::now()->subDays(5),
            'status' => 'overdue'
        ]);

        Employee::create([
            'user_id' => $instructor->id,
            'designation' => 'Senior Instructor',
            'base_salary' => 120000.00,
            'hire_date' => Carbon::now()->subMonths(6),
            'is_active' => true
        ]);

        LibraryBook::create([
            'isbn' => '978-3-16-148410-0',
            'title' => 'Artificial Intelligence: A Modern Approach',
            'author' => 'Stuart Russell and Peter Norvig',
            'total_copies' => 10,
            'available_copies' => 8
        ]);

        $this->command->info('Extended LMS and ERP tables seeded successfully.');
    }
}
