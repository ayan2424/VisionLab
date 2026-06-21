@extends('layouts.dashboard')
@section('title', 'Instructor Dashboard')
@section('page-title', 'Command Center')

@section('content')
<style>
    /* ═══════════════════════════════════════════════════════════════════
       INSTRUCTOR DASHBOARD — Premium Dark Design System
       Consistent with landing page + student dashboard aesthetic
       ═══════════════════════════════════════════════════════════════════ */
    .dash-section { margin-bottom: 2rem; }

    /* Stat Cards */
    .stat-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    .stat-card-custom {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 1rem;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        transform-style: preserve-3d;
    }
    .stat-card-custom:hover {
        border-color: rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.04);
        transform: perspective(800px) translateY(-4px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }
    .stat-card-custom::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        background: radial-gradient(300px circle at var(--mouse-x, 50%) var(--mouse-y, 50%),
            rgba(255,255,255,0.03), transparent 40%);
        opacity: 0;
        transition: opacity 0.3s;
        pointer-events: none;
    }
    .stat-card-custom:hover::before { opacity: 1; }
    .stat-icon {
        width: 36px; height: 36px;
        border-radius: 0.65rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        font-size: 1rem;
    }
    .stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1;
        margin-bottom: 0.25rem;
        color: white;
    }
    .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 600;
        color: var(--vc-text-secondary, #a1a1aa);
    }

    /* Content cards */
    .content-card {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 1rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .content-card:hover {
        border-color: rgba(255,255,255,0.1);
    }
    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .card-header-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: white;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .card-body { padding: 1.25rem 1.5rem; }

    /* Course items */
    .course-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.85rem;
        border-radius: 0.75rem;
        transition: background 0.2s;
        cursor: pointer;
        text-decoration: none;
    }
    .course-item:hover { background: rgba(255,255,255,0.03); }
    .course-avatar {
        width: 36px; height: 36px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        color: white;
        flex-shrink: 0;
    }

    /* Submission items */
    .submission-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem;
        border-radius: 0.75rem;
        transition: background 0.2s;
    }
    .submission-item:hover { background: rgba(255,255,255,0.03); }
    .status-badge {
        font-size: 0.6rem;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .status-submitted { background: rgba(0,229,255,0.12); color: #00e5ff; }
    .status-graded { background: rgba(16,185,129,0.12); color: #34d399; }

    /* Action buttons */
    .action-btn {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-radius: 0.75rem;
        border: 1px solid rgba(255,255,255,0.06);
        background: rgba(255,255,255,0.02);
        color: white;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.25s ease;
    }
    .action-btn:hover {
        border-color: rgba(168,85,247,0.3);
        background: rgba(168,85,247,0.05);
        transform: translateY(-1px);
    }
    .action-icon {
        width: 32px; height: 32px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }

    /* Alert card */
    .alert-card {
        border-color: rgba(245,158,11,0.2);
        background: rgba(245,158,11,0.03);
    }

    /* Telemetry row */
    .telemetry-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .telemetry-card {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 0.75rem;
        padding: 1.25rem;
        text-align: center;
    }
    .telemetry-value {
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: -0.02em;
    }
    .telemetry-label {
        font-size: 0.65rem;
        color: var(--vc-text-secondary, #71717a);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 600;
        margin-top: 0.25rem;
    }

    /* Animation */
    .fade-in {
        animation: dashFadeIn 0.6s ease forwards;
        opacity: 0;
    }
    @keyframes dashFadeIn {
        to { opacity: 1; transform: translateY(0); }
        from { opacity: 0; transform: translateY(12px); }
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--vc-text-secondary, #52525b);
    }
    .empty-icon {
        font-size: 2rem;
        margin-bottom: 0.75rem;
        opacity: 0.4;
    }
</style>

<div class="dash-section">
    <!-- Header -->
    <div class="fade-in" style="margin-bottom:2rem;animation-delay:0.05s">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem">
            <div>
                <h2 style="font-size:1.5rem;font-weight:800;letter-spacing:-0.02em;margin-bottom:0.25rem">
                    Instructor Deck, {{ explode(' ', Auth::user()->name)[0] }}
                </h2>
                <p style="font-size:0.8rem;color:var(--vc-text-secondary, #71717a)">
                    {{ now()->format('l, F j, Y') }} · Manage your courses and students
                </p>
            </div>
            <div style="display:flex;gap:0.5rem">
                <a href="{{ route('courses.create') }}" class="action-btn" style="border-color:rgba(0,229,255,0.2);background:rgba(0,229,255,0.05)">
                    <span style="color:#00e5ff">+</span> New Course
                </a>
                <a href="{{ route('submissions.queue') }}" class="action-btn">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg> Grade Queue
                </a>
            </div>
        </div>
    </div>

    <!-- Primary Stats -->
    <div class="stat-row fade-in" style="animation-delay:0.1s">
        <div class="stat-card-custom">
            <div class="stat-icon" style="background:rgba(168,85,247,0.12);color:#a855f7">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </div>
            <div class="stat-value">{{ $courses->count() }}</div>
            <div class="stat-label">My Courses</div>
        </div>
        <div class="stat-card-custom">
            <div class="stat-icon" style="background:rgba(0,229,255,0.12);color:#00e5ff">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <div class="stat-value">{{ $totalStudents ?? 0 }}</div>
            <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-card-custom">
            <div class="stat-icon" style="background:rgba(245,158,11,0.12);color:#f59e0b">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
            <div class="stat-value" style="{{ ($pendingGrading ?? 0) > 0 ? 'color:#fbbf24' : '' }}">{{ $pendingGrading ?? 0 }}</div>
            <div class="stat-label">Pending Grading</div>
        </div>
        <div class="stat-card-custom">
            <div class="stat-icon" style="background:rgba(16,185,129,0.12);color:#10b981">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            </div>
            <div class="stat-value">{{ $avgGrade ?? '—' }}%</div>
            <div class="stat-label">Avg Class Grade</div>
        </div>
    </div>

    <!-- Secondary Telemetry -->
    <div class="telemetry-row fade-in" style="animation-delay:0.15s">
        <div class="telemetry-card">
            <div class="telemetry-value" style="color:#f87171">{{ $lateSubmissions ?? 0 }}</div>
            <div class="telemetry-label">Late Submissions</div>
        </div>
        <div class="telemetry-card">
            <div class="telemetry-value" style="color:#00e5ff">{{ $activeWorkspaceCount ?? 0 }}</div>
            <div class="telemetry-label">Active Workspaces</div>
        </div>
        <div class="telemetry-card">
            <div class="telemetry-value" style="color:#a855f7">{{ $pendingPatches ?? 0 }}</div>
            <div class="telemetry-label">AI Patches Pending</div>
        </div>
    </div>

    <!-- Content Grid -->
    <div style="display:grid;grid-template-columns:1fr;gap:1.25rem" class="content-grid-lg fade-in" style="animation-delay:0.2s">

        <!-- LEFT COLUMN -->
        <div style="display:flex;flex-direction:column;gap:1.25rem">
            <!-- Active Courses -->
            <div class="content-card">
                <div class="card-header">
                    <span class="card-header-title">
                        <span style="color:#a855f7">●</span> Active Environments
                    </span>
                    <a href="{{ route('courses.index') }}" style="font-size:0.7rem;color:var(--vc-text-secondary, #71717a);text-decoration:none">Manage →</a>
                </div>
                <div class="card-body">
                    @if($courses->count() > 0)
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:0.75rem">
                            @foreach($courses->take(6) as $course)
                            @php
                                $colors = ['#7c3aed','#0891b2','#059669','#db2777','#ea580c','#2563eb'];
                                $color = $colors[$loop->index % count($colors)];
                            @endphp
                            <a href="{{ route('courses.show', $course) }}" class="course-item">
                                <div class="course-avatar" style="background:{{ $color }}22;color:{{ $color }}">
                                    {{ strtoupper(substr($course->title, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-size:0.85rem;font-weight:600;color:white">{{ Str::limit($course->title, 30) }}</div>
                                    <div style="font-size:0.7rem;color:var(--vc-text-secondary, #71717a)">
                                        {{ $course->enrollments_count ?? $course->enrollments()->count() }} students
                                        @if($course->enrollment_code)
                                        · <span style="font-family:monospace;color:var(--vc-text-secondary, #52525b)">{{ $course->enrollment_code }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon text-purple-500/40">
                                <svg class="w-8 h-8 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            </div>
                            <p style="font-size:0.85rem;font-weight:600;margin-bottom:0.25rem">No courses yet</p>
                            <p style="font-size:0.75rem;margin-bottom:1rem">Create your first course</p>
                            <a href="{{ route('courses.create') }}" class="action-btn" style="display:inline-flex">Create Course</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Submissions -->
            <div class="content-card">
                <div class="card-header">
                    <span class="card-header-title">
                        <span style="color:#00e5ff">●</span> Live Feed
                    </span>
                </div>
                <div class="card-body">
                    @if(isset($recentSubmissions) && $recentSubmissions->count() > 0)
                        <div style="display:flex;flex-direction:column;gap:0.5rem">
                            @foreach($recentSubmissions->take(6) as $sub)
                            @php
                                $statusClass = $sub->grade !== null ? 'status-graded' : 'status-submitted';
                                $statusText = $sub->grade !== null ? 'Graded' : 'Submitted';
                            @endphp
                            <div class="submission-item">
                                <div style="display:flex;align-items:center;gap:0.75rem">
                                    <div style="width:28px;height:28px;border-radius:50%;background:rgba(168,85,247,0.12);display:flex;align-items:center;justify-content:center;font-size:0.6rem;font-weight:700;color:#a855f7">
                                        {{ strtoupper(substr($sub->student->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-size:0.8rem;font-weight:600;color:white">{{ $sub->student->name ?? 'Student' }}</div>
                                        <div style="font-size:0.65rem;color:var(--vc-text-secondary, #52525b)">{{ Str::limit($sub->assignment->title ?? '', 30) }}</div>
                                    </div>
                                </div>
                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state" style="padding:1.5rem">
                            <div class="empty-icon text-blue-500/40">
                                <svg class="w-8 h-8 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <p style="font-size:0.75rem">No submissions yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div style="display:flex;flex-direction:column;gap:1.25rem">
            <!-- Action Required Alert -->
            @if(($pendingGrading ?? 0) > 0)
            <div class="content-card alert-card">
                <div class="card-body" style="display:flex;gap:0.75rem;align-items:flex-start">
                    <div style="font-size:1.25rem;color:#fbbf24;margin-top:2px;">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div>
                        <div style="font-size:0.8rem;font-weight:700;color:#fbbf24;margin-bottom:0.25rem">Action Required</div>
                        <p style="font-size:0.7rem;color:var(--vc-text-secondary, #71717a);line-height:1.5;margin-bottom:0.75rem">
                            You have <strong style="color:white">{{ $pendingGrading }}</strong> {{ Str::plural('submission', $pendingGrading) }} waiting to be graded.
                        </p>
                        <a href="{{ route('grading.queue') }}" class="action-btn" style="display:inline-flex;font-size:0.75rem;padding:0.5rem 0.85rem">
                            Open Grade Queue →
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="content-card">
                <div class="card-header">
                    <span class="card-header-title">
                        <span style="color:#ec4899">●</span> Master Controls
                    </span>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:0.5rem">
                    <a href="{{ route('courses.create') }}" class="action-btn">
                        <div class="action-icon" style="background:rgba(168,85,247,0.1);color:#a855f7">+</div>
                        Deploy New Course
                    </a>
                    <a href="{{ route('grading.queue') }}" class="action-btn">
                        <div class="action-icon" style="background:rgba(0,229,255,0.1);color:#00e5ff">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        </div>
                        Review Submissions
                    </a>
                    <a href="{{ route('workspace.index') }}" class="action-btn">
                        <div class="action-icon" style="background:rgba(16,185,129,0.1);color:#10b981">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        Launch Master IDE
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3D Tilt for stat cards -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.stat-card-custom').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const midX = rect.width / 2;
            const midY = rect.height / 2;
            card.style.transform = `perspective(800px) rotateX(${((y - midY) / midY) * -6}deg) rotateY(${((x - midX) / midX) * 6}deg) scale(1.02)`;
            card.style.setProperty('--mouse-x', x + 'px');
            card.style.setProperty('--mouse-y', y + 'px');
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(800px) rotateX(0) rotateY(0) scale(1)';
        });
    });
});
</script>

<style>
@media (min-width: 1024px) {
    .content-grid-lg { grid-template-columns: 2fr 1fr !important; }
}
</style>
@endsection
