<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminWorkspaceController;
use App\Http\Controllers\Admin\ExtensionController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Admin\AiSettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/demo', fn () => view('demo'))->name('demo');
Route::get('/offline', fn () => view('offline'))->name('offline');
Route::get('/demo-script', fn () => view('demo'))->name('demo-script')->middleware(['auth', 'role:admin,instructor']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Workspace ──────────────────────────────────────────────────────
    Route::get('/workspace', [\App\Http\Controllers\WorkspaceController::class, 'index'])->name('workspace.index');
    Route::get('/workspace/{workspace}', [\App\Http\Controllers\WorkspaceController::class, 'show'])->name('workspace.show');

    // ── Collaborative Rooms ────────────────────────────────────────────
    Route::post('/rooms', [\App\Http\Controllers\RoomController::class, 'create'])->name('rooms.create');

    // ── Courses ────────────────────────────────────────────────────────
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course:slug}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course:slug}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course:slug}', [CourseController::class, 'destroy'])->name('courses.destroy');

    // ── Enrollments ────────────────────────────────────────────────────
    Route::get('/join', [EnrollmentController::class, 'joinForm'])->name('enrollments.join');
    Route::post('/join', [EnrollmentController::class, 'joinByCode'])->name('enrollments.join.post');
    Route::post('/courses/{course:slug}/invite', [EnrollmentController::class, 'inviteStudent'])->name('enrollments.invite');
    Route::delete('/courses/{course:slug}/students/{studentId}', [EnrollmentController::class, 'remove'])->name('enrollments.remove');

    // ── Assignments ────────────────────────────────────────────────────
    Route::get('/courses/{course:slug}/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/courses/{course:slug}/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
    Route::get('/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
    Route::put('/assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

    // ── Submissions ────────────────────────────────────────────────────
    Route::post('/assignments/{assignment}/start',          [SubmissionController::class, 'start'])->name('submissions.start');
    Route::get('/assignments/{assignment}/ide',             [SubmissionController::class, 'ide'])->name('submissions.ide');
    Route::post('/assignments/{assignment}/save-snapshot',  [SubmissionController::class, 'saveSnapshot'])->name('submissions.save');
    Route::post('/assignments/{assignment}/submit',         [SubmissionController::class, 'submit'])->name('submissions.submit');
    Route::get('/submissions',                              [SubmissionController::class, 'queue'])->name('submissions.queue');
    Route::get('/submissions/{submission}',                 [SubmissionController::class, 'show'])->name('submissions.show');
    Route::patch('/submissions/{submission}/grade',         [SubmissionController::class, 'grade'])->name('submissions.grade');

    // ── Announcements ──────────────────────────────────────────────────
    Route::post('/courses/{course:slug}/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // ── Progress ───────────────────────────────────────────────────────
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');

    // ── Profile ────────────────────────────────────────────────────────
    Route::get('/profile',    [ProfileController::class, 'edit'])  ->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Admin Panel ────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
        Route::post('/users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Extensions
        Route::get('/extensions', [ExtensionController::class, 'index'])->name('extensions.index');
        Route::get('/extensions/create', [ExtensionController::class, 'create'])->name('extensions.create');
        Route::post('/extensions', [ExtensionController::class, 'store'])->name('extensions.store');
        Route::get('/extensions/{extension}/edit', [ExtensionController::class, 'edit'])->name('extensions.edit');
        Route::put('/extensions/{extension}', [ExtensionController::class, 'update'])->name('extensions.update');
        Route::patch('/extensions/{extension}/toggle', [ExtensionController::class, 'toggleGlobal'])->name('extensions.toggle');
        Route::delete('/extensions/{extension}', [ExtensionController::class, 'destroy'])->name('extensions.destroy');

        // AI Settings
        Route::get('/ai-settings', [AiSettingsController::class, 'index'])->name('ai-settings.index');
        Route::post('/ai-settings/keys', [AiSettingsController::class, 'updateKeys'])->name('ai-settings.keys');
        Route::post('/ai-settings/features', [AiSettingsController::class, 'updateFeatures'])->name('ai-settings.features');
        Route::post('/ai-settings/models', [AiSettingsController::class, 'storeModel'])->name('ai-settings.models.store');
        Route::patch('/ai-settings/models/{model}/toggle', [AiSettingsController::class, 'toggleModel'])->name('ai-settings.models.toggle');
        Route::patch('/ai-settings/models/{model}/default', [AiSettingsController::class, 'setDefault'])->name('ai-settings.models.default');
        Route::delete('/ai-settings/models/{model}', [AiSettingsController::class, 'destroyModel'])->name('ai-settings.models.destroy');
        Route::post('/ai-settings/test-connection', [AiSettingsController::class, 'testConnection'])->name('ai-settings.test');

        // Analytics
        Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');

        // Workspaces
        Route::get('/workspaces', [AdminWorkspaceController::class, 'index'])->name('workspaces.index');
        Route::get('/workspaces/{workspace}', [AdminWorkspaceController::class, 'show'])->name('workspaces.show');
        Route::post('/workspaces/{workspace}/stop', [AdminWorkspaceController::class, 'stop'])->name('workspaces.stop');
        Route::delete('/workspaces/{workspace}', [AdminWorkspaceController::class, 'destroy'])->name('workspaces.destroy');
    });
});



require __DIR__.'/auth.php';
