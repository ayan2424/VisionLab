# [Project Completion Boost - 3 Quick Wins]

The goal of this phase is to rapidly increase the completion percentage of the project from 25% to 40%+ by implementing three core MVC features that do not rely on complex external dependencies (like Docker or Jitsi).

## User Review Required

> [!IMPORTANT]
> Please review this plan and give approval. Once approved, we will execute these three tasks back-to-back.

## Open Questions

- Should we design the Admin Dashboard with hardcoded stat cards first, or directly link them to the database counts (`User::count()`, `Workspace::count()`, etc.) right away? (Recommendation: Link to DB right away since it's easy).

## Proposed Changes

---

### 1. FormRequests (Security Validations)

This will fulfill Phase 11's security validation requirement (OWASP). We will create `FormRequest` classes to validate incoming HTTP requests strictly.

#### [NEW] [StoreWorkspaceRequest.php](file:///c:/Users/ayans/OneDrive/Documents/A%20Projects/Aptech/Vision2026/VisionLab/app/Http/Requests/StoreWorkspaceRequest.php)
- Rules for `name`, `language_template`, `ram_limit`.

#### [NEW] [StoreAssignmentRequest.php](file:///c:/Users/ayans/OneDrive/Documents/A%20Projects/Aptech/Vision2026/VisionLab/app/Http/Requests/StoreAssignmentRequest.php)
- Rules for `title`, `description`, `due_date`, `max_score`.

#### [NEW] [SubmitAssignmentRequest.php](file:///c:/Users/ayans/OneDrive/Documents/A%20Projects/Aptech/Vision2026/VisionLab/app/Http/Requests/SubmitAssignmentRequest.php)
- Rules to ensure submission content is present and not empty.

---

### 2. Global Announcements System (Phase 2)

This will complete the announcement module of the LMS domain. The model `Announcement.php` already exists.

#### [NEW] [AnnouncementController.php](file:///c:/Users/ayans/OneDrive/Documents/A%20Projects/Aptech/Vision2026/VisionLab/app/Http/Controllers/AnnouncementController.php)
- `index()` for students to view announcements.
- `store()` for instructors/admins to post markdown announcements.

#### [NEW] [announcements/index.blade.php](file:///c:/Users/ayans/OneDrive/Documents/A%20Projects/Aptech/Vision2026/VisionLab/resources/views/announcements/index.blade.php)
- UI to display all announcements as a feed.

#### [NEW] [announcements/create.blade.php](file:///c:/Users/ayans/OneDrive/Documents/A%20Projects/Aptech/Vision2026/VisionLab/resources/views/announcements/create.blade.php)
- UI for instructors to create an announcement using a textarea.

---

### 3. Admin Live Dashboard (Phase 8)

This builds the foundation for Phase 8 governance. We will create a visually appealing dashboard for the Admin role.

#### [NEW] [AdminDashboardController.php](file:///c:/Users/ayans/OneDrive/Documents/A%20Projects/Aptech/Vision2026/VisionLab/app/Http/Controllers/Admin/AdminDashboardController.php)
- Queries to get totals: Users, Courses, Workspaces, Assignments.

#### [NEW] [admin/dashboard.blade.php](file:///c:/Users/ayans/OneDrive/Documents/A%20Projects/Aptech/Vision2026/VisionLab/resources/views/admin/dashboard.blade.php)
- A Tailwind CSS powered dashboard with 6 stat cards and a basic recent activity table.

---

## Verification Plan

### Automated Tests
- We can write a simple test for `StoreWorkspaceRequest` to ensure validation fails on empty input.

### Manual Verification
- Login as Admin and view the dashboard stats.
- Login as Instructor, post an announcement, and verify students can see it.
- Send a bad request to the Workspace creation endpoint and verify a 422 Validation Error is returned.
