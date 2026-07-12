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
use App\Http\Controllers\Admin\ExtensionController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/healthz', [\App\Http\Controllers\HealthController::class, 'check'])->name('health');
Route::get('/demo', fn () => view('demo'))->name('demo');
Route::get('/offline', fn () => view('offline'))->name('offline');

// ── SEO Public Pages ──────────────────────────────────────────────────
Route::get('/about', fn () => view('pages.about'))->name('about');
Route::get('/features', fn () => view('pages.features'))->name('features');
Route::get('/contact', fn () => view('pages.contact'))->name('contact');
Route::get('/docs', fn () => view('pages.docs'))->name('docs');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

    // Phase 10: PWA Offline Fallback
    Route::view('/offline', 'errors.offline')->name('offline');

    // ── Workspace ──────────────────────────────────────────────────────
    Route::get('/workspace', [\App\Http\Controllers\WorkspaceController::class, 'index'])->name('workspace.index');
    Route::get('/workspace/create', [\App\Http\Controllers\WorkspaceController::class, 'create'])->name('workspace.create');
    Route::post('/workspace', [\App\Http\Controllers\WorkspaceController::class, 'store'])->name('workspace.store');
    Route::get('/workspace/{workspace:slug}/rebuild', [\App\Http\Controllers\WorkspaceController::class, 'rebuildView'])->name('workspace.rebuild');
    Route::post('/workspace/{workspace:slug}/rebuild/process', [\App\Http\Controllers\WorkspaceController::class, 'processRebuild'])->name('workspace.process_rebuild');
    Route::get('/workspace/{workspace:slug}/restart', [\App\Http\Controllers\WorkspaceController::class, 'restartView'])->name('workspace.restart');
    Route::post('/workspace/{workspace:slug}/restart/process', [\App\Http\Controllers\WorkspaceController::class, 'processRestart'])->name('workspace.process_restart');
    Route::get('/workspace/{workspace}', [\App\Http\Controllers\WorkspaceController::class, 'show'])->name('workspace.show');
    Route::post('/workspace/{workspace}/start', [\App\Http\Controllers\WorkspaceController::class, 'start'])->name('workspace.start');
    Route::post('/workspace/{workspace}/stop', [\App\Http\Controllers\WorkspaceController::class, 'stop'])->name('workspace.stop');

    Route::delete('/workspace/{workspace}', [\App\Http\Controllers\WorkspaceController::class, 'destroy'])->name('workspace.destroy');
    Route::get('/workspace/{workspace}/status', [\App\Http\Controllers\WorkspaceController::class, 'status'])->name('workspace.status');
    Route::get('/workspace/{workspace}/ping', [\App\Http\Controllers\WorkspaceController::class, 'ping'])->name('workspace.ping');

    // ── Workspace File I/O ──────────────────────────────────────────────
    Route::get('/workspace/{workspace}/files', [\App\Http\Controllers\WorkspaceController::class, 'files'])->name('workspace.files');
    Route::get('/workspace/{workspace}/read-file', [\App\Http\Controllers\WorkspaceController::class, 'readFile'])->name('workspace.readFile');
    Route::get('/workspace/{workspace}/download-file', [\App\Http\Controllers\WorkspaceController::class, 'downloadFile'])->name('workspace.downloadFile');
    Route::post('/workspace/{workspace}/write-file', [\App\Http\Controllers\WorkspaceController::class, 'writeFile'])->name('workspace.writeFile');
    Route::post('/workspace/{workspace}/create-file', [\App\Http\Controllers\WorkspaceController::class, 'createFile'])->name('workspace.createFile');
    Route::delete('/workspace/{workspace}/delete-file', [\App\Http\Controllers\WorkspaceController::class, 'deleteFile'])->name('workspace.deleteFile');
    Route::post('/workspace/{workspace}/rename-file', [\App\Http\Controllers\WorkspaceController::class, 'renameFile'])->name('workspace.renameFile');

    // ── Workspace Extension Manager ────────────────────────────────────
    Route::post('/workspace/{workspace}/extensions/{extension}/install', [\App\Http\Controllers\WorkspaceExtensionManagerController::class, 'install'])->name('workspace.extensions.install');
    Route::delete('/workspace/{workspace}/extensions/{extension}/uninstall', [\App\Http\Controllers\WorkspaceExtensionManagerController::class, 'uninstall'])->name('workspace.extensions.uninstall');

    // ── Collaborative Rooms & Chat ─────────────────────────────────────
    Route::post('/rooms', [\App\Http\Controllers\RoomController::class, 'create'])->name('rooms.create');
    Route::post('/workspace/{workspace}/chat', [\App\Http\Controllers\ChatController::class, 'store'])->name('workspace.chat');

    // ── Courses ────────────────────────────────────────────────────────
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{slug}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{slug}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{slug}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::get('/courses/{course:slug}/roster', [\App\Http\Controllers\CourseController::class, 'roster'])->name('courses.roster');
    Route::put('/courses/{course:slug}/extensions', [\App\Http\Controllers\CourseExtensionController::class, 'update'])->name('courses.extensions.update');

    // ── Enrollments ────────────────────────────────────────────────────
    Route::get('/join', [EnrollmentController::class, 'joinForm'])->name('enrollments.join');
    Route::post('/join', [EnrollmentController::class, 'joinByCode'])->name('enrollments.join.post');
    Route::post('/courses/{course:slug}/invite', [EnrollmentController::class, 'invite'])->name('enrollments.invite');
    Route::post('/courses/{course:slug}/enrollments/add', [EnrollmentController::class, 'addByStudentId'])->name('enrollments.add_by_id');
    Route::post('/courses/{course:slug}/import-csv', [EnrollmentController::class, 'importCsv'])->name('enrollments.import_csv');
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
    Route::get('/grading/queue',                            [SubmissionController::class, 'queue'])->name('grading.queue');
    Route::get('/submissions/{submission}',                 [SubmissionController::class, 'show'])->name('submissions.show');
    Route::patch('/submissions/{submission}/grade',         [SubmissionController::class, 'grade'])->name('submissions.grade');

    // ── Announcements ──────────────────────────────────────────────────
    Route::get('/announcements', [\App\Http\Controllers\GlobalAnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/create', [\App\Http\Controllers\GlobalAnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [\App\Http\Controllers\GlobalAnnouncementController::class, 'store'])->name('announcements.store_global');
    Route::delete('/announcements/global/{announcement}', [\App\Http\Controllers\GlobalAnnouncementController::class, 'destroy'])->name('announcements.destroy_global');
    Route::post('/courses/{course:slug}/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::post('/announcements/{announcement}/read', [\App\Http\Controllers\AnnouncementReadController::class, 'markAsRead'])->name('announcements.read');

    // ── Progress ───────────────────────────────────────────────────────
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');

    // ── Contributions (Heatmap) ────────────────────────────────────────
    Route::get('/api/contributions', [\App\Http\Controllers\ContributionController::class, 'heatmap'])->name('contributions.heatmap');

    // ── Deployments ────────────────────────────────────────────────────
    Route::post('/workspace/{workspace}/deploy', [\App\Http\Controllers\DeploymentController::class, 'deploy'])->name('workspace.deploy');
    Route::get('/workspace/{workspace}/deployments', [\App\Http\Controllers\DeploymentController::class, 'index'])->name('workspace.deployments');
    Route::get('/deployments/{deployment}/status', [\App\Http\Controllers\DeploymentController::class, 'status'])->name('deployments.status');
    Route::delete('/deployments/{deployment}', [\App\Http\Controllers\DeploymentController::class, 'destroy'])->name('deployments.destroy');
    Route::get('/my-deployments', function() {
        return view('deployments.index', [
            'deployments' => \App\Models\Deployment::where('user_id', auth()->id())->latest()->get(),
            'workspaces' => \App\Models\Workspace::where('user_id', auth()->id())->get()
        ]);
    })->name('deployments.index');

    // ── Profile ────────────────────────────────────────────────────────
    Route::get('/profile/dashboard', [ProfileController::class, 'index'])  ->name('profile.index');
    Route::get('/profile',    [ProfileController::class, 'edit'])  ->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Admin Panel ────────────────────────────────────────────────────
    Route::middleware(['role:admin', 'throttle:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/impersonate', [AdminUserController::class, 'impersonate'])->name('users.impersonate');
        Route::get('/users/{user}/export', [AdminUserController::class, 'exportGdpr'])->name('users.export');
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

        // Workspaces
        Route::get('/workspaces', [\App\Http\Controllers\Admin\AdminWorkspaceController::class, 'index'])->name('workspaces.index');
        Route::get('/workspaces/{workspace}', [\App\Http\Controllers\Admin\AdminWorkspaceController::class, 'show'])->name('workspaces.show');
        Route::post('/workspaces/{workspace}/stop', [\App\Http\Controllers\Admin\AdminWorkspaceController::class, 'stop'])->name('workspaces.stop');
        Route::post('/workspaces/{workspace}/archive', [\App\Http\Controllers\Admin\AdminWorkspaceController::class, 'archive'])->name('workspaces.archive');
        Route::delete('/workspaces/{workspace}', [\App\Http\Controllers\Admin\AdminWorkspaceController::class, 'destroy'])->name('workspaces.destroy');
        Route::patch('/workspaces/{workspace}/extensions/{extension}/toggle', [\App\Http\Controllers\Admin\AdminWorkspaceController::class, 'toggleExtension'])->name('workspaces.extensions.toggle');

        // Workspace Templates
        Route::resource('templates', \App\Http\Controllers\Admin\AdminTemplateController::class)->except(['show']);

        // Quotas
        Route::get('/quotas', [\App\Http\Controllers\Admin\AdminQuotaController::class, 'index'])->name('quotas.index');
        Route::post('/quotas', [\App\Http\Controllers\Admin\AdminQuotaController::class, 'updateGlobal'])->name('quotas.updateGlobal');

        // Institute ERP (Campus, Department, Course Batch)
        Route::resource('campuses', \App\Http\Controllers\Admin\AdminCampusController::class);
        Route::resource('departments', \App\Http\Controllers\Admin\AdminDepartmentController::class);
        Route::resource('batches', \App\Http\Controllers\Admin\AdminCourseBatchController::class);

        // Extended ERP & LMS
        Route::resource('challans', \App\Http\Controllers\Admin\AdminFeeChallanController::class);
        Route::resource('employees', \App\Http\Controllers\Admin\AdminEmployeeController::class);
        Route::resource('library', \App\Http\Controllers\Admin\AdminLibraryController::class);

        // Audit Logs
        Route::get('/audits', [\App\Http\Controllers\Admin\AdminAuditController::class, 'index'])->name('audits.index');

        // Analytics
        Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');

        // System Configs
        Route::get('/system', [\App\Http\Controllers\Admin\AdminSystemController::class, 'index'])->name('system.index');
        Route::post('/system/flags', [\App\Http\Controllers\Admin\AdminSystemController::class, 'updateFlags'])->name('system.flags.update');
        Route::post('/system/maintenance', [\App\Http\Controllers\Admin\AdminSystemController::class, 'toggleMaintenance'])->name('system.maintenance.toggle');

        // Webhooks
        Route::resource('webhooks', \App\Http\Controllers\Admin\AdminWebhookController::class)->except(['show']);

        // System Status (infrastructure probe dashboard — admin-only)
        Route::get('/status', [\App\Http\Controllers\HealthController::class, 'status'])->name('status');
    });
});

// Stop impersonation route (Needs to be inside `auth` but NOT `admin` middleware, because the impersonated user might not be an admin!)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/stop-impersonating', [\App\Http\Controllers\Admin\AdminUserController::class, 'stopImpersonating'])->name('stop_impersonating');
});

require __DIR__.'/auth.php';
