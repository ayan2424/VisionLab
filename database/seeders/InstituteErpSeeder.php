<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Semester;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\GradeItem;
use App\Models\Assignment;
use App\Models\Rubric;
use App\Models\RubricCriteria;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class InstituteErpSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Campus
        $campus = Campus::updateOrCreate(
            ['code' => 'MAIN'],
            [
                'name' => 'VisionTech Main Campus',
                'city' => 'Metropolis',
                'is_active' => true,
            ]
        );

        // 2. Create Departments
        $csDept = Department::updateOrCreate(
            ['campus_id' => $campus->id, 'code' => 'CS'],
            [
                'name' => 'Computer Science',
                'description' => 'Department of Computer Science & Engineering',
                'is_active' => true,
            ]
        );

        $seDept = Department::updateOrCreate(
            ['campus_id' => $campus->id, 'code' => 'SE'],
            [
                'name' => 'Software Engineering',
                'description' => 'Department of Software Engineering',
                'is_active' => true,
            ]
        );

        // 3. Create Semester
        $semester = Semester::updateOrCreate(
            ['term' => 'Fall 2026', 'campus_id' => $campus->id],
            [
                'year' => 2026,
                'start_date' => Carbon::create(2026, 9, 1),
                'end_date' => Carbon::create(2026, 12, 31),
                'is_active' => true,
            ]
        );

        // 4. Update Admin User with Campus/Dept
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->update(['campus_id' => $campus->id]);
        }

        // Create an Instructor if none exists
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@visionlab.edu'],
            [
                'name' => 'Dr. Alan Turing',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'status' => 'active',
                'campus_id' => $campus->id,
                'department_id' => $csDept->id,
            ]
        );

        // Create a Student if none exists
        $student = User::firstOrCreate(
            ['email' => 'student@visionlab.edu'],
            [
                'name' => 'Ada Lovelace',
                'password' => Hash::make('password'),
                'role' => 'student',
                'status' => 'active',
                'student_id' => 'STU-2026-001',
                'campus_id' => $campus->id,
                'department_id' => $csDept->id,
            ]
        );

        // 5. Create a Course
        $course = Course::updateOrCreate(
            ['slug' => 'cs101-intro-to-programming'],
            [
                'instructor_id' => $instructor->id,
                'title' => 'CS101: Introduction to Programming',
                'description' => 'Fundamentals of programming using Python.',
                'semester_id' => $semester->id,
                'is_active' => true,
            ]
        );

        // 6. Create a Course Batch (Section)
        $batch = CourseBatch::updateOrCreate(
            ['course_id' => $course->id, 'title' => 'Section A'],
            [
                'department_id' => $csDept->id,
                'semester_id' => $semester->id,
                'instructor_id' => $instructor->id,
                'timing' => 'Mon/Wed 10:00 AM',
                'start_date' => $semester->start_date,
                'max_capacity' => 50,
                'room_number' => 'Lab 402',
                'is_active' => true,
            ]
        );

        // 7. Enroll the student in the batch
        $course->students()->syncWithoutDetaching([
            $student->id => [
                'status' => 'active',
                'batch_id' => $batch->id,
                'fee_status' => 'paid',
                'enrolled_at' => now(),
            ]
        ]);

        // 8. Create Grade Items
        $gradeItemAssignments = GradeItem::updateOrCreate(
            ['course_id' => $course->id, 'name' => 'Assignments'],
            ['weight' => 40.00, 'sort_order' => 1]
        );

        $gradeItemMidterm = GradeItem::updateOrCreate(
            ['course_id' => $course->id, 'name' => 'Midterm Exam'],
            ['weight' => 20.00, 'sort_order' => 2]
        );

        $gradeItemFinal = GradeItem::updateOrCreate(
            ['course_id' => $course->id, 'name' => 'Final Exam'],
            ['weight' => 40.00, 'sort_order' => 3]
        );

        // 9. Create an Assignment with Rubric
        $assignment = Assignment::updateOrCreate(
            ['course_id' => $course->id, 'title' => 'Assignment 1: Hello World'],
            [
                'grade_item_id' => $gradeItemAssignments->id,
                'description' => 'Write a Python program that prints Hello World.',
                'max_points' => 100,
                'due_date' => Carbon::now()->addDays(7),
                'starter_language' => 'python',
                'starter_code' => "def main():\n    pass\n\nif __name__ == '__main__':\n    main()",
                'auto_workspace' => true,
            ]
        );

        // 10. Create Rubric
        $rubric = Rubric::updateOrCreate(
            ['assignment_id' => $assignment->id],
            [
                'title' => 'Standard Code Quality Rubric',
                'total_points' => 100,
            ]
        );

        RubricCriteria::updateOrCreate(
            ['rubric_id' => $rubric->id, 'title' => 'Correctness'],
            ['description' => 'The program runs without errors and produces the correct output.', 'max_points' => 60, 'sort_order' => 1]
        );

        RubricCriteria::updateOrCreate(
            ['rubric_id' => $rubric->id, 'title' => 'Code Style'],
            ['description' => 'Code is well-formatted, uses meaningful variable names, and follows PEP 8.', 'max_points' => 40, 'sort_order' => 2]
        );

        $this->command->info('Institute ERP Seeded Successfully!');
    }
}
