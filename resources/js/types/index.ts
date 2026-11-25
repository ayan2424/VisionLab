export type UserRole = 'admin' | 'instructor' | 'student' | 'subscriber';

export interface AuthUser {
    id: number;
    name: string;
    email: string;
    role: UserRole;
    status: string;
    student_id: string | null;
    avatar_initials: string;
    xp: number;
    level: number;
    rank_title: string;
    current_streak: number;
}

export interface FlashMessages {
    status?: string | null;
    success?: string | null;
    error?: string | null;
}

export interface PortalNotification {
    id: number;
    title: string;
    body: string;
    pinned: boolean;
    course_title: string | null;
    course_slug: string | null;
    is_unread: boolean;
    created_human: string;
}

export interface SharedProps {
    auth: {
        user: AuthUser | null;
        impersonating: boolean;
    };
    flash: FlashMessages;
    unreadAnnouncementsCount: number;
    recentNotifications: PortalNotification[];
    csrfToken: string;
    errors: Record<string, string>;
    [key: string]: unknown;
}
