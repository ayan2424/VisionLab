@extends('layouts.dashboard')
@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<style>
    /* ═══════════════════════════════════════════════════════════════════
       STUDENT DASHBOARD — Premium Dark Design System
       Matches landing page aesthetic: clean, minimal, subtle 3D
       ═══════════════════════════════════════════════════════════════════ */
    .dash-section { margin-bottom: 2rem; }

    /* Stat Cards */
    .stat-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
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
    .course-name { font-size: 0.85rem; font-weight: 600; color: white; }
    .course-instructor { font-size: 0.7rem; color: var(--vc-text-secondary, #71717a); }

    /* Assignment items */
    .assignment-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem;
        border-radius: 0.75rem;
        transition: background 0.2s;
    }
    .assignment-item:hover { background: rgba(255,255,255,0.03); }
    .assignment-title { font-size: 0.85rem; font-weight: 600; color: white; }
    .assignment-course { font-size: 0.7rem; color: var(--vc-text-secondary, #71717a); }
    .due-badge {
        font-size: 0.65rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        white-space: nowrap;
    }
    .due-urgent { background: rgba(239,68,68,0.15); color: #f87171; }
    .due-soon { background: rgba(245,158,11,0.15); color: #fbbf24; }
    .due-normal { background: rgba(16,185,129,0.15); color: #34d399; }

    /* Announcement items */
    .announcement-item {
        padding: 0.85rem;
        border-radius: 0.75rem;
        transition: background 0.2s;
    }
    .announcement-item:hover { background: rgba(255,255,255,0.03); }
    .announcement-title { font-size: 0.8rem; font-weight: 600; color: white; margin-bottom: 0.15rem; }
    .announcement-time { font-size: 0.65rem; color: var(--vc-text-secondary, #52525b); }
    .unread-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: #a855f7;
        box-shadow: 0 0 8px #a855f7;
        flex-shrink: 0;
    }

    /* Quick actions */
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

    /* Grade bar */
    .grade-bar-bg {
        height: 4px;
        border-radius: 2px;
        background: rgba(255,255,255,0.06);
        overflow: hidden;
        margin-top: 0.5rem;
    }
    .grade-bar-fill {
        height: 100%;
        border-radius: 2px;
        transition: width 1s ease;
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
    <!-- Welcome Header -->
    <div class="fade-in" style="margin-bottom:2rem;animation-delay:0.05s">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem">
            <div>
                <h2 style="font-size:1.5rem;font-weight:800;letter-spacing:-0.02em;margin-bottom:0.25rem">
                    Welcome back, {{ explode(' ', Auth::user()->name)[0] }} 👋
                </h2>
                <p style="font-size:0.8rem;color:var(--vc-text-secondary, #71717a)">
                    {{ now()->format('l, F j, Y') }} · {{ $courses->count() }} active {{ Str::plural('course', $courses->count()) }}
                </p>
            </div>
            <div style="display:flex;gap:0.5rem">
                <a href="{{ route('workspace.index') }}" class="action-btn" style="border-color:rgba(0,229,255,0.2);background:rgba(0,229,255,0.05)">
                    <span style="color:#00e5ff">⚡</span> Launch IDE
                </a>
                <a href="{{ route('courses.index') }}" class="action-btn">
                    <span>📚</span> Browse Courses
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stat-row fade-in" style="animation-delay:0.1s">
        <div class="stat-card-custom">
            <div class="stat-icon" style="background:rgba(168,85,247,0.12)">📘</div>
            <div class="stat-value" style="color:white">{{ $courses->count() }}</div>
            <div class="stat-label">Active Courses</div>
        </div>
        <div class="stat-card-custom">
            <div class="stat-icon" style="background:rgba(0,229,255,0.12)">📝</div>
            <div class="stat-value" style="color:white">{{ $pendingSubmissions ?? 0 }}</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card-custom">
            <div class="stat-icon" style="background:rgba(245,158,11,0.12)">⏰</div>
            <div class="stat-value" style="color:white">{{ isset($upcomingAssignments) ? $upcomingAssignments->count() : 0 }}</div>
            <div class="stat-label">Due Soon</div>
        </div>
        <div class="stat-card-custom">
            <div class="stat-icon" style="background:rgba(236,72,153,0.12)">💬</div>
            <div class="stat-value" style="color:white">{{ $unreadAnnouncementCount ?? 0 }}</div>
            <div class="stat-label">Unread</div>
        </div>
        <div class="stat-card-custom">
            <div class="stat-icon" style="background:rgba(16,185,129,0.12)">🔥</div>
            <div class="stat-value" style="background:linear-gradient(to right,#a855f7,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $streak ?? 0 }}</div>
            <div class="stat-label">Day Streak</div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div style="display:grid;grid-template-columns:1fr;gap:1.25rem" class="fade-in">

        <div style="display:grid;grid-template-columns:1fr;gap:1.25rem" class="content-grid-lg">
            <!-- LEFT COLUMN -->
            <div style="display:flex;flex-direction:column;gap:1.25rem">
                <!-- Active Courses -->
                <div class="content-card">
                    <div class="card-header">
                        <span class="card-header-title">
                            <span style="color:#a855f7">●</span> Active Courses
                        </span>
                        <a href="{{ route('courses.index') }}" style="font-size:0.7rem;color:var(--vc-text-secondary, #71717a);text-decoration:none">View all →</a>
                    </div>
                    <div class="card-body">
                        @if($courses->count() > 0)
                            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:0.75rem">
                                @foreach($courses->take(6) as $course)
                                @php
                                    $colors = ['#7c3aed','#0891b2','#059669','#db2777','#ea580c','#2563eb'];
                                    $color = $colors[$loop->index % count($colors)];
                                @endphp
                                <a href="{{ route('courses.show', $course) }}" class="course-item" style="text-decoration:none">
                                    <div class="course-avatar" style="background:{{ $color }}22;color:{{ $color }}">
                                        {{ strtoupper(substr($course->title, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="course-name">{{ Str::limit($course->title, 30) }}</div>
                                        <div class="course-instructor">{{ $course->instructor->name ?? 'No instructor' }}</div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">📚</div>
                                <p style="font-size:0.85rem;font-weight:600;margin-bottom:0.25rem">No courses yet</p>
                                <p style="font-size:0.75rem;margin-bottom:1rem">Join a course to get started</p>
                                <a href="{{ route('courses.index') }}" class="action-btn" style="display:inline-flex">Join a Course</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Announcements & Grades sub-grid -->
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.25rem">
                    <!-- Announcements -->
                    <div class="content-card">
                        <div class="card-header">
                            <span class="card-header-title">
                                <span style="color:#00e5ff">●</span> Announcements
                            </span>
                        </div>
                        <div class="card-body">
                            @if(isset($recentAnnouncements) && $recentAnnouncements->count() > 0)
                                <div style="display:flex;flex-direction:column;gap:0.5rem">
                                    @foreach($recentAnnouncements->take(4) as $announcement)
                                    <div class="announcement-item" style="display:flex;align-items:flex-start;gap:0.75rem">
                                        @if(!$announcement->is_read)
                                            <div class="unread-dot" style="margin-top:0.4rem"></div>
                                        @endif
                                        <div style="flex:1">
                                            <div class="announcement-title">{{ Str::limit($announcement->title, 40) }}</div>
                                            <div class="announcement-time">{{ $announcement->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state" style="padding:1.5rem">
                                    <div class="empty-icon">📢</div>
                                    <p style="font-size:0.75rem">No announcements</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Grades -->
                    <div class="content-card">
                        <div class="card-header">
                            <span class="card-header-title">
                                <span style="color:#10b981">●</span> Recent Grades
                            </span>
                        </div>
                        <div class="card-body">
                            @if(isset($gradedSubmissions) && $gradedSubmissions->count() > 0)
                                <div style="display:flex;flex-direction:column;gap:0.75rem">
                                    @foreach($gradedSubmissions->take(4) as $sub)
                                    @php
                                        $pct = $sub->assignment && $sub->assignment->max_points > 0
                                            ? round(($sub->grade / $sub->assignment->max_points) * 100)
                                            : 0;
                                        $barColor = $pct >= 80 ? '#10b981' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
                                    @endphp
                                    <div>
                                        <div style="display:flex;justify-content:space-between;align-items:center">
                                            <span style="font-size:0.8rem;font-weight:600;color:white">{{ Str::limit($sub->assignment->title ?? 'Assignment', 25) }}</span>
                                            <span style="font-size:0.75rem;font-weight:700;color:{{ $barColor }}">{{ $pct }}%</span>
                                        </div>
                                        <div class="grade-bar-bg">
                                            <div class="grade-bar-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state" style="padding:1.5rem">
                                    <div class="empty-icon">📊</div>
                                    <p style="font-size:0.75rem">No grades yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div style="display:flex;flex-direction:column;gap:1.25rem">
                <!-- Upcoming Assignments -->
                <div class="content-card">
                    <div class="card-header">
                        <span class="card-header-title">
                            <span style="color:#f59e0b">●</span> Upcoming
                        </span>
                    </div>
                    <div class="card-body">
                        @if(isset($upcomingAssignments) && $upcomingAssignments->count() > 0)
                            <div style="display:flex;flex-direction:column;gap:0.5rem">
                                @foreach($upcomingAssignments->take(5) as $assignment)
                                @php
                                    $daysLeft = now()->diffInDays($assignment->due_date, false);
                                    $badgeClass = $daysLeft <= 1 ? 'due-urgent' : ($daysLeft <= 3 ? 'due-soon' : 'due-normal');
                                    $badgeText = $daysLeft < 0 ? 'Overdue' : ($daysLeft == 0 ? 'Today' : ($daysLeft == 1 ? 'Tomorrow' : $daysLeft . 'd left'));
                                @endphp
                                <div class="assignment-item">
                                    <div>
                                        <div class="assignment-title">{{ Str::limit($assignment->title, 28) }}</div>
                                        <div class="assignment-course">{{ $assignment->course->title ?? '' }}</div>
                                    </div>
                                    <span class="due-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state" style="padding:1.5rem">
                                <div class="empty-icon">✅</div>
                                <p style="font-size:0.75rem">All caught up!</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="content-card">
                    <div class="card-header">
                        <span class="card-header-title">
                            <span style="color:#ec4899">●</span> Quick Actions
                        </span>
                    </div>
                    <div class="card-body" style="display:flex;flex-direction:column;gap:0.5rem">
                        <a href="{{ route('workspace.index') }}" class="action-btn">
                            <div class="action-icon" style="background:rgba(0,229,255,0.1);color:#00e5ff">⚡</div>
                            Workspace IDE
                        </a>
                        <a href="{{ route('courses.index') }}" class="action-btn">
                            <div class="action-icon" style="background:rgba(168,85,247,0.1);color:#a855f7">📘</div>
                            My Courses
                        </a>
                    </div>
                </div>

                <!-- AI Tip -->
                <div class="content-card" style="border-color:rgba(168,85,247,0.15);background:rgba(168,85,247,0.02)">
                    <div class="card-body" style="display:flex;gap:0.75rem;align-items:flex-start">
                        <span style="font-size:1.25rem">🤖</span>
                        <div>
                            <div style="font-size:0.8rem;font-weight:700;color:white;margin-bottom:0.25rem">AI Agent Tip</div>
                            <p style="font-size:0.7rem;color:var(--vc-text-secondary, #71717a);line-height:1.5">
                                Try switching to <strong style="color:#a855f7">Socratic Mode</strong> for guided learning — it asks questions instead of giving answers.
                            </p>
                        </div>
                    </div>
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
