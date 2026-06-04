<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Submission;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

// Setup test data
DB::statement('PRAGMA foreign_keys = OFF;');
Assignment::truncate();
Course::truncate();
User::truncate();
Enrollment::truncate();
Submission::truncate();
PushSubscription::truncate();
DB::statement('PRAGMA foreign_keys = ON;');

$instructor = User::create([
    'name' => "Instructor",
    'email' => "instructor@test.com",
    'password' => bcrypt('password'),
    'role' => 'instructor',
]);

$course = Course::create(['title' => 'Test Course', 'instructor_id' => $instructor->id, 'description' => 'Test']);

$assignment = Assignment::create([
    'course_id' => $course->id,
    'title' => 'Test Assignment',
    'description' => 'Test',
    'due_date' => now()->addHours(12),
    'max_points' => 100,
]);

// Create 50 students
for ($i = 1; $i <= 50; $i++) {
    $student = User::create([
        'name' => "Student $i",
        'email' => "student$i@test.com",
        'password' => bcrypt('password'),
        'role' => 'student',
    ]);

    Enrollment::create([
        'course_id' => $course->id,
        'student_id' => $student->id,
        'status' => 'active',
    ]);

    PushSubscription::create([
        'user_id' => $student->id,
        'endpoint' => "https://endpoint.com/sub$i",
    ]);

    if ($i % 2 == 0) {
        Submission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'status' => 'submitted',
        ]);
    }
}

DB::enableQueryLog();

$startTime = microtime(true);
Artisan::call('notify:due-assignments');
$endTime = microtime(true);

$queries = DB::getQueryLog();
echo "Number of queries executed: " . count($queries) . "\n";
echo "Execution time: " . ($endTime - $startTime) . " seconds\n";
