<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * INSTITUTE_ERP_HIERARCHY — Phase 1 & 2 Migration
 *
 * Adds the foundational ERP hierarchy that the LMS, IDE, and AI features attach to.
 *
 * New tables:  campuses, departments, attendance_logs, rubrics, rubric_criteria, grade_items
 * Augmented:   users, semesters, course_batches, enrollments, submissions, submission_forensics
 */
return new class extends Migration
{
    public function up(): void
    {
        // ══════════════════════════════════════════════════════════════════
        // NEW TABLES
        // ══════════════════════════════════════════════════════════════════

        // ── campuses ────────────────────────────────────────────────────
        if (! Schema::hasTable('campuses')) {
            Schema::create('campuses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 20)->unique();
                $table->string('address')->nullable();
                $table->string('city', 100)->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('email')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ── departments ─────────────────────────────────────────────────
        if (! Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('campus_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('code', 20);
                $table->text('description')->nullable();
                $table->foreignId('head_of_dept_id')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['campus_id', 'code']);
                $table->index('campus_id');
            });
        }

        // ── attendance_logs ─────────────────────────────────────────────
        if (! Schema::hasTable('attendance_logs')) {
            Schema::create('attendance_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('batch_id')->constrained('course_batches')->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->date('date');
                $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
                $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->unique(['batch_id', 'student_id', 'date']);
                $table->index(['student_id', 'date']);
                $table->index(['batch_id', 'date']);
            });
        }

        // ── rubrics ─────────────────────────────────────────────────────
        if (! Schema::hasTable('rubrics')) {
            Schema::create('rubrics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedSmallInteger('total_points')->default(100);
                $table->timestamps();

                $table->index('assignment_id');
            });
        }

        // ── rubric_criteria ─────────────────────────────────────────────
        if (! Schema::hasTable('rubric_criteria')) {
            Schema::create('rubric_criteria', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rubric_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedSmallInteger('max_points');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index('rubric_id');
            });
        }

        // ── grade_items (weighted gradebook categories) ─────────────────
        if (! Schema::hasTable('grade_items')) {
            Schema::create('grade_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->decimal('weight', 5, 2);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index('course_id');
            });
        }

        // ══════════════════════════════════════════════════════════════════
        // AUGMENT EXISTING TABLES
        // ══════════════════════════════════════════════════════════════════

        // ── Augment: users ──────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'campus_id')) {
                $table->unsignedBigInteger('campus_id')->nullable()->after('role');
                $table->foreign('campus_id')->references('id')->on('campuses')->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('campus_id');
                $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('date_of_birth');
            }
            if (! Schema::hasColumn('users', 'guardian_name')) {
                $table->string('guardian_name')->nullable()->after('address');
            }
            if (! Schema::hasColumn('users', 'guardian_phone')) {
                $table->string('guardian_phone', 30)->nullable()->after('guardian_name');
            }
        });

        // ── Augment: semesters ──────────────────────────────────────────
        Schema::table('semesters', function (Blueprint $table) {
            if (! Schema::hasColumn('semesters', 'year')) {
                $table->unsignedSmallInteger('year')->nullable()->after('term');
            }
            if (! Schema::hasColumn('semesters', 'campus_id')) {
                $table->unsignedBigInteger('campus_id')->nullable()->after('year');
                $table->foreign('campus_id')->references('id')->on('campuses')->nullOnDelete();
            }
            if (! Schema::hasColumn('semesters', 'is_active')) {
                $table->boolean('is_active')->default(false)->after('end_date');
            }
        });

        // ── Augment: course_batches ─────────────────────────────────────
        Schema::table('course_batches', function (Blueprint $table) {
            if (! Schema::hasColumn('course_batches', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('course_id');
                $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            }
            if (! Schema::hasColumn('course_batches', 'semester_id')) {
                $table->unsignedBigInteger('semester_id')->nullable()->after('department_id');
                $table->foreign('semester_id')->references('id')->on('semesters')->nullOnDelete();
            }
            if (! Schema::hasColumn('course_batches', 'instructor_id')) {
                $table->unsignedBigInteger('instructor_id')->nullable()->after('semester_id');
                $table->foreign('instructor_id')->references('id')->on('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('course_batches', 'max_capacity')) {
                $table->unsignedSmallInteger('max_capacity')->nullable()->after('timing');
            }
            if (! Schema::hasColumn('course_batches', 'room_number')) {
                $table->string('room_number', 30)->nullable()->after('max_capacity');
            }
        });

        // ── Augment: enrollments ────────────────────────────────────────
        Schema::table('enrollments', function (Blueprint $table) {
            if (! Schema::hasColumn('enrollments', 'batch_id')) {
                $table->unsignedBigInteger('batch_id')->nullable()->after('course_id');
                $table->foreign('batch_id')->references('id')->on('course_batches')->nullOnDelete();
            }
            if (! Schema::hasColumn('enrollments', 'fee_status')) {
                $table->enum('fee_status', ['pending', 'partial', 'paid', 'waived'])->default('pending')->after('status');
            }
        });

        // ── Augment: assignments ────────────────────────────────────────
        Schema::table('assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('assignments', 'grade_item_id')) {
                $table->unsignedBigInteger('grade_item_id')->nullable()->after('course_id');
                $table->foreign('grade_item_id')->references('id')->on('grade_items')->nullOnDelete();
            }
            if (! Schema::hasColumn('assignments', 'time_limit_minutes')) {
                $table->unsignedSmallInteger('time_limit_minutes')->nullable()->after('due_date');
            }
        });

        // ── Augment: submissions ────────────────────────────────────────
        Schema::table('submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('submissions', 'workspace_id')) {
                $table->unsignedBigInteger('workspace_id')->nullable()->after('student_id');
                $table->foreign('workspace_id')->references('id')->on('workspaces')->nullOnDelete();
            }
            if (! Schema::hasColumn('submissions', 'rubric_scores')) {
                $table->json('rubric_scores')->nullable()->after('grade');
            }
        });

        // ── Augment: submission_forensics ────────────────────────────────
        if (Schema::hasTable('submission_forensics')) {
            Schema::table('submission_forensics', function (Blueprint $table) {
                if (! Schema::hasColumn('submission_forensics', 'paste_event_count')) {
                    $table->unsignedInteger('paste_event_count')->default(0)->after('confidence');
                }
                if (! Schema::hasColumn('submission_forensics', 'paste_char_total')) {
                    $table->unsignedInteger('paste_char_total')->default(0)->after('paste_event_count');
                }
                if (! Schema::hasColumn('submission_forensics', 'focus_loss_count')) {
                    $table->unsignedInteger('focus_loss_count')->default(0)->after('paste_char_total');
                }
                if (! Schema::hasColumn('submission_forensics', 'total_idle_seconds')) {
                    $table->unsignedInteger('total_idle_seconds')->default(0)->after('focus_loss_count');
                }
                if (! Schema::hasColumn('submission_forensics', 'keystroke_count')) {
                    $table->unsignedInteger('keystroke_count')->default(0)->after('total_idle_seconds');
                }
                if (! Schema::hasColumn('submission_forensics', 'flagged')) {
                    $table->boolean('flagged')->default(false)->after('keystroke_count');
                }
                if (! Schema::hasColumn('submission_forensics', 'flag_reason')) {
                    $table->text('flag_reason')->nullable()->after('flagged');
                }
            });
        }
    }

    public function down(): void
    {
        // Drop augmented columns (reverse order)
        if (Schema::hasTable('submission_forensics')) {
            Schema::table('submission_forensics', function (Blueprint $table) {
                $cols = ['paste_event_count', 'paste_char_total', 'focus_loss_count',
                         'total_idle_seconds', 'keystroke_count', 'flagged', 'flag_reason'];
                $existing = array_filter($cols, fn($c) => Schema::hasColumn('submission_forensics', $c));
                if ($existing) $table->dropColumn($existing);
            });
        }

        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'workspace_id')) {
                $table->dropForeign(['workspace_id']);
                $table->dropColumn('workspace_id');
            }
            if (Schema::hasColumn('submissions', 'rubric_scores')) {
                $table->dropColumn('rubric_scores');
            }
        });

        Schema::table('assignments', function (Blueprint $table) {
            if (Schema::hasColumn('assignments', 'grade_item_id')) {
                $table->dropForeign(['grade_item_id']);
                $table->dropColumn('grade_item_id');
            }
            if (Schema::hasColumn('assignments', 'time_limit_minutes')) {
                $table->dropColumn('time_limit_minutes');
            }
        });

        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'batch_id')) {
                $table->dropForeign(['batch_id']);
                $table->dropColumn('batch_id');
            }
            if (Schema::hasColumn('enrollments', 'fee_status')) {
                $table->dropColumn('fee_status');
            }
        });

        Schema::table('course_batches', function (Blueprint $table) {
            foreach (['department_id', 'semester_id', 'instructor_id'] as $fk) {
                if (Schema::hasColumn('course_batches', $fk)) {
                    $table->dropForeign([$fk]);
                    $table->dropColumn($fk);
                }
            }
            foreach (['max_capacity', 'room_number'] as $col) {
                if (Schema::hasColumn('course_batches', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('semesters', function (Blueprint $table) {
            if (Schema::hasColumn('semesters', 'campus_id')) {
                $table->dropForeign(['campus_id']);
                $table->dropColumn('campus_id');
            }
            foreach (['year', 'is_active'] as $col) {
                if (Schema::hasColumn('semesters', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            foreach (['campus_id', 'department_id'] as $fk) {
                if (Schema::hasColumn('users', $fk)) {
                    $table->dropForeign([$fk]);
                    $table->dropColumn($fk);
                }
            }
            foreach (['phone', 'date_of_birth', 'address', 'guardian_name', 'guardian_phone'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        // Drop new tables
        Schema::dropIfExists('grade_items');
        Schema::dropIfExists('rubric_criteria');
        Schema::dropIfExists('rubrics');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('campuses');
    }
};
