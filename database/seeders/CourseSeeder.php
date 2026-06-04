<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Create extra instructors and students
        $instructor2 = User::updateOrCreate(['email' => 'instructor2@VisionLab.ai'], [
            'name' => 'Instructor Mark', 'password' => Hash::make('Instructor@12345'),
            'role' => 'instructor', 'theme_preference' => 'dark', 'email_verified_at' => now(), 'status' => 'active',
        ]);

        $students = [];
        $studentData = [
            ['name' => 'Student Alex',    'email' => 'student@VisionLab.ai'],
            ['name' => 'Student Sara',    'email' => 'student2@VisionLab.ai'],
            ['name' => 'Student Jordan',  'email' => 'student3@VisionLab.ai'],
            ['name' => 'Student Priya',   'email' => 'student4@VisionLab.ai'],
            ['name' => 'Student Liam',    'email' => 'student5@VisionLab.ai'],
        ];
        foreach ($studentData as $s) {
            $students[] = User::updateOrCreate(['email' => $s['email']], [
                'name' => $s['name'], 'password' => Hash::make('Student@12345'),
                'role' => 'student', 'theme_preference' => 'dark', 'email_verified_at' => now(), 'status' => 'active',
            ]);
        }

        $instructor1 = User::where('email', 'instructor@VisionLab.ai')->first();

        // Course 1 - Python & AI Fundamentals
        $course1 = Course::updateOrCreate(['enrollment_code' => 'PYAI26'], [
            'instructor_id'   => $instructor1->id,
            'title'           => 'Python & AI Fundamentals',
            'slug'            => 'python-ai-fundamentals-' . str_pad($instructor1->id, 3, '0', STR_PAD_LEFT),
            'description'     => 'Master Python programming from basics to advanced AI integration. Build real-world ML models and explore modern AI APIs. Perfect for students who want to break into AI/ML development.',
            'is_active'       => true,
        ]);

        // Course 2 - Full-Stack Web with Laravel
        $course2 = Course::updateOrCreate(['enrollment_code' => 'LARA26'], [
            'instructor_id'   => $instructor1->id,
            'title'           => 'Full-Stack Web with Laravel',
            'slug'            => 'full-stack-web-laravel-' . str_pad($instructor1->id, 3, '0', STR_PAD_LEFT),
            'description'     => 'Build production-grade web applications with Laravel 11, Blade, Tailwind CSS, and MySQL. Learn RBAC, REST APIs, WebSockets with Reverb, and deployment strategies.',
            'is_active'       => true,
        ]);

        // Course 3 - Algorithms & Data Structures
        $course3 = Course::updateOrCreate(['enrollment_code' => 'ALGO26'], [
            'instructor_id'   => $instructor2->id,
            'title'           => 'Algorithms & Data Structures',
            'slug'            => 'algorithms-data-structures-' . str_pad($instructor2->id, 3, '0', STR_PAD_LEFT),
            'description'     => 'Deep dive into algorithms, complexity analysis, sorting, trees, graphs, and dynamic programming. Ace technical interviews and write efficient code.',
            'is_active'       => true,
        ]);

        // Enroll students
        foreach ($students as $student) {
            Enrollment::updateOrCreate(
                ['course_id' => $course1->id, 'student_id' => $student->id],
                ['status' => 'active', 'enrolled_at' => now()->subDays(rand(10, 30))]
            );
        }
        foreach (array_slice($students, 0, 3) as $student) {
            Enrollment::updateOrCreate(
                ['course_id' => $course2->id, 'student_id' => $student->id],
                ['status' => 'active', 'enrolled_at' => now()->subDays(rand(5, 20))]
            );
        }
        foreach (array_slice($students, 2, 4) as $student) {
            Enrollment::updateOrCreate(
                ['course_id' => $course3->id, 'student_id' => $student->id],
                ['status' => 'active', 'enrolled_at' => now()->subDays(rand(3, 15))]
            );
        }

        // Assignments for Course 1
        $a1 = Assignment::updateOrCreate(['course_id' => $course1->id, 'title' => 'Python Basics — Variables & Control Flow'], [
            'description'      => "Write a Python program that:\n1. Asks the user for their name and age\n2. Calculates the year they were born\n3. Determines if they are old enough to vote\n4. Prints a formatted summary\n\nBonus: Handle edge cases (negative age, future year).",
            'max_points'       => 100,
            'due_date'         => now()->addDays(7),
            'starter_language' => 'python',
            'starter_code'     => "# Python Basics — Variables & Control Flow\n# Complete the functions below\n\ndef get_user_info():\n    \"\"\"Prompt the user for name and age. Return (name, age) tuple.\"\"\"\n    # TODO: implement\n    pass\n\ndef calculate_birth_year(age: int) -> int:\n    \"\"\"Calculate birth year from age.\"\"\"\n    # TODO: implement\n    pass\n\ndef can_vote(age: int) -> bool:\n    \"\"\"Return True if age >= 18.\"\"\"\n    # TODO: implement\n    pass\n\nif __name__ == '__main__':\n    name, age = get_user_info()\n    # TODO: print formatted summary\n",
            'auto_workspace'   => true,
        ]);

        $a2 = Assignment::updateOrCreate(['course_id' => $course1->id, 'title' => 'Functions & Recursion — Fibonacci & Sorting'], [
            'description'      => "Implement the following algorithms in Python:\n1. Fibonacci sequence (both iterative and recursive)\n2. Binary search on a sorted list\n3. Merge sort\n\nInclude docstrings, type hints, and unit tests for each function.",
            'max_points'       => 150,
            'due_date'         => now()->addDays(14),
            'starter_language' => 'python',
            'starter_code'     => "# Functions & Recursion\nfrom typing import List\n\ndef fibonacci_iterative(n: int) -> int:\n    \"\"\"Return the nth Fibonacci number iteratively.\"\"\"\n    pass\n\ndef fibonacci_recursive(n: int) -> int:\n    \"\"\"Return the nth Fibonacci number recursively.\"\"\"\n    pass\n\ndef binary_search(arr: List[int], target: int) -> int:\n    \"\"\"Return the index of target in sorted arr, or -1 if not found.\"\"\"\n    pass\n\ndef merge_sort(arr: List[int]) -> List[int]:\n    \"\"\"Return a sorted copy of arr using merge sort.\"\"\"\n    pass\n",
            'auto_workspace'   => true,
        ]);

        // Past assignment
        $a3 = Assignment::updateOrCreate(['course_id' => $course1->id, 'title' => 'Intro to AI APIs — Claude & Gemini'], [
            'description'      => "Integrate an AI API into a Python script:\n1. Call the Gemini API with a prompt\n2. Parse and display the response\n3. Implement a simple CLI chatbot that maintains conversation history",
            'max_points'       => 200,
            'due_date'         => now()->subDays(3),
            'starter_language' => 'python',
            'starter_code'     => "import os\nimport google.generativeai as genai\n\n# Configure the API\ngenai.configure(api_key=os.environ.get('GEMINI_API_KEY', ''))\n\n# Initialize the model\nmodel = genai.GenerativeModel('gemini-1.5-flash')\n\ndef chat_with_ai(history: list, user_message: str) -> str:\n    \"\"\"Send a message and get AI response.\"\"\"\n    chat = model.start_chat(history=history)\n    response = chat.send_message(user_message)\n    # Update history in place so it persists across calls\n    history.clear()\n    for msg in chat.history:\n        history.append(msg)\n    return response.text\n\nif __name__ == '__main__':\n    history = []\n    print('VisionLabChat — type quit to exit')\n    while True:\n        msg = input('You: ')\n        if msg.lower() == 'quit':\n            break\n        reply = chat_with_ai(history, msg)\n        print(f'AI: {reply}')\n",
            'auto_workspace'   => true,
        ]);

        // Create some submissions for past assignment
        foreach (array_slice($students, 0, 3) as $i => $student) {
            Submission::updateOrCreate(
                ['assignment_id' => $a3->id, 'student_id' => $student->id],
                [
                    'status'       => $i === 0 ? 'graded' : 'submitted',
                    'submitted_at' => now()->subDays(rand(1, 3)),
                    'code_snapshot'=> "import os\nimport google.generativeai as genai\n\ngenai.configure(api_key=os.environ.get('GEMINI_API_KEY'))\nmodel = genai.GenerativeModel('gemini-2.0-flash')\n\ndef chat_with_ai(history, msg):\n    chat = model.start_chat(history=history)\n    response = chat.send_message(msg)\n    return response.text\n\nif __name__ == '__main__':\n    history = []\n    while True:\n        msg = input('You: ')\n        if msg == 'quit': break\n        reply = chat_with_ai(history, msg)\n        print(f'AI: {reply}')\n",
                    'grade'        => $i === 0 ? 185 : null,
                    'feedback'     => $i === 0 ? 'Excellent implementation! Clean code and proper error handling.' : null,
                    'graded_by'    => $i === 0 ? $instructor1->id : null,
                ]
            );
        }

        // Announcements for Course 1
        Announcement::updateOrCreate(
            ['course_id' => $course1->id, 'title' => 'Welcome to Python & AI Fundamentals!'],
            ['author_id' => $instructor1->id, 'body' => "Welcome to the course! This semester we'll be exploring Python from the ground up and applying it to real-world AI tasks.\n\n**Please do the following by next week:**\n- Install Python 3.11+\n- Join our workspace using the enrollment code\n- Complete Assignment 1\n\nFeel free to ask questions in the workspace chat. Let's build something amazing together!", 'pinned' => true]
        );
        Announcement::updateOrCreate(
            ['course_id' => $course1->id, 'title' => 'Assignment 3 now available — AI APIs'],
            ['author_id' => $instructor1->id, 'body' => "Assignment 3 is now live! You'll be integrating the Gemini API into a Python chatbot. This is the most exciting assignment yet — you'll get hands-on experience with production AI APIs.\n\n**Due date: 3 days from now.** Start early, the API integration requires debugging!", 'pinned' => false]
        );

        Announcement::updateOrCreate(
            ['course_id' => $course2->id, 'title' => 'Laravel Course — Environment Setup'],
            ['author_id' => $instructor1->id, 'body' => "Welcome to Full-Stack Web with Laravel! Your workspace already has PHP 8.3, Composer, and Node.js installed.\n\nFor this course, all coding will happen inside VisionLabworkspaces — no local setup needed! The AI agent will help you debug Laravel issues.", 'pinned' => true]
        );

        $this->command->info('✅ Seeded: 3 courses, 5 students, 3 assignments, submissions, and announcements');
        $this->command->info('   Course codes: PYAI26 | LARA26 | ALGO26');
    }
}
