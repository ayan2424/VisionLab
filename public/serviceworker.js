const CACHE_NAME = 'visionlab-v1';
const STATIC_ASSETS = [
    '/offline',
    '/logo.svg',
    '/manifest.json'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Skip cross-origin requests, API routes, and Reverb websocket
    if (url.origin !== location.origin || url.pathname.startsWith('/api') || url.pathname.startsWith('/app')) {
        return;
    }

    // Do NOT cache Workspace routes (IDE must be live)
    if (url.pathname.startsWith('/workspace/')) {
        event.respondWith(
            fetch(event.request).catch(() => {
                // If offline and trying to load workspace, show offline fallback page
                return caches.match('/offline');
            })
        );
        return;
    }

    // Network-first strategy for dynamic HTML pages
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => {
                return caches.match('/offline');
            })
        );
        return;
    }

    // Stale-while-revalidate for static assets (CSS, JS, images)
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            const fetchPromise = fetch(event.request).then((networkResponse) => {
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, networkResponse.clone());
                });
                return networkResponse;
            }).catch(() => null);

            return cachedResponse || fetchPromise;
        })
    );
});

// Push Notification Handling
self.addEventListener('push', function(event) {
    if (!event.data) return;
    
    let data = {};
    try {
        data = event.data.json();
    } catch (e) {
        data = { title: 'VisionLab', body: event.data.text() };
    }

    const options = {
        body: data.body,
        icon: '/logo.svg',
        badge: '/logo.svg',
        vibrate: [100, 50, 100],
        data: {
            url: data.url || '/dashboard'
        }
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'New Notification', options)
    );
});

// Click notification handler
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    if (event.notification.data && event.notification.data.url) {
        event.waitUntil(
            clients.openWindow(event.notification.data.url)
        );
    }
});
