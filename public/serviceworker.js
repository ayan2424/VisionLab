const CACHE_NAME = 'visioncode-v1';
const OFFLINE_URL = '/offline';

const PRECACHE_ASSETS = [
    '/',
    '/offline',
    '/manifest.json',
];

const NAVIGATION_ROUTES = [
    '/dashboard', '/courses', '/workspace', '/admin'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(PRECACHE_ASSETS).catch(() => {});
        }).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET, cross-origin, WebSocket
    if (request.method !== 'GET' || url.origin !== location.origin || url.pathname.startsWith('/api/')) {
        return;
    }

    // Workspace: network only
    if (url.pathname.startsWith('/workspace')) {
        return;
    }

    // Static assets: cache first
    if (url.pathname.match(/\.(css|js|png|jpg|jpeg|svg|ico|woff2?|ttf)$/)) {
        event.respondWith(
            caches.match(request).then(cached => cached || fetch(request).then(res => {
                const clone = res.clone();
                caches.open(CACHE_NAME).then(c => c.put(request, clone));
                return res;
            }))
        );
        return;
    }

    // Navigation: network first, fallback offline
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match('/offline') || new Response('Offline', { status: 503 }))
        );
        return;
    }
});

// Push notifications
self.addEventListener('push', event => {
    const data = event.data ? event.data.json() : {};
    event.waitUntil(
        self.registration.showNotification(data.title || 'VisionLab', {
            body: data.body || 'You have a new notification.',
            icon: '/icons/icon-192.png',
            badge: '/icons/icon-192.png',
            data: { url: data.url || '/dashboard' },
            vibrate: [100, 50, 100]
        })
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url || '/dashboard')
    );
});
