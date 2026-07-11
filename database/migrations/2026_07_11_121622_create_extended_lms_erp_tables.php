<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ---------------------------------------------------------
        // LMS Core Expansion
        // ---------------------------------------------------------

        // 1. Modules
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        // 2. Lessons
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_module_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['video', 'pdf', 'text']);
            $table->string('content_url')->nullable();
            $table->text('body')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        // 3. Quizzes
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_module_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->integer('time_limit_minutes')->nullable();
            $table->integer('passing_score')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Questions
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer']);
            $table->json('options')->nullable();
            $table->string('correct_answer');
            $table->integer('points')->default(1);
            $table->timestamps();
        });

        // 5. Quiz Attempts
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('score')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 6. Grading Rubrics
        Schema::create('grading_rubrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->string('criteria');
            $table->integer('max_points');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 7. Forum Topics
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });

        // 8. Forum Posts (Replies)
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_topic_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('body');
            $table->timestamps();
        });

        // 9. Certificates
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('certificate_code')->unique();
            $table->timestamp('issued_at');
            $table->string('pdf_url')->nullable();
            $table->timestamps();
        });


        // ---------------------------------------------------------
        // ERP Core Expansion
        // ---------------------------------------------------------

        // 10. Fee Challans
        Schema::create('fee_challans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Student
            $table->string('challan_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->date('due_date');
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->date('paid_date')->nullable();
            $table->timestamps();
        });

        // 11. Transactions (Finance Ledger)
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->foreignId('fee_challan_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });

        // 12. Employees
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Instructor/Admin
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->string('designation');
            $table->decimal('base_salary', 10, 2);
            $table->date('hire_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 13. Payroll
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('month_year'); // e.g. "07-2026"
            $table->decimal('basic_pay', 10, 2);
            $table->decimal('allowances', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('net_pay', 10, 2);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->timestamps();
        });

        // 14. Attendance
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Can be student or employee
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'leave'])->default('present');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->timestamps();
        });

        // 15. Library Books
        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn')->nullable();
            $table->string('title');
            $table->string('author');
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->timestamps();
        });

        // 16. Book Issues
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_book_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['issued', 'returned', 'lost'])->default('issued');
            $table->decimal('fine_amount', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_issues');
        Schema::dropIfExists('library_books');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('fee_challans');
        
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_topics');
        Schema::dropIfExists('grading_rubrics');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('course_modules');
    }
};
