var staticCacheName = "pwa-v1-" + new Date().getTime();
var filesToCache = [
    '/offline',
    '/build/assets/app.css',
    '/build/assets/app.js',
    '/manifest.json'
];

// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache or Network
self.addEventListener("fetch", event => {
    // Only intercept GET requests
    if (event.request.method !== 'GET') return;

    // Ignore requests to external domains
    if (!event.request.url.startsWith(self.location.origin)) return;

    // Ignore API, Livewire, Reverb/Websockets, and Telescope
    if (event.request.url.includes('/api/') || 
        event.request.url.includes('/livewire/') || 
        event.request.url.includes('/app/') || 
        event.request.url.includes('/telescope')) {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request)
                    .then(fetchResponse => {
                        return fetchResponse;
                    })
                    .catch(() => {
                        // Return the offline page if network fails and it's a navigation request
                        if (event.request.mode === 'navigate') {
                            return caches.match('/offline');
                        }
                    });
            })
    );
});

// Push Notifications Listener
self.addEventListener('push', function(event) {
    if (event.data) {
        const data = event.data.json();
        const options = {
            body: data.body || 'You have a new notification',
            icon: data.icon || '/icons/icon-192.png',
            badge: '/icons/icon-192.png',
            vibrate: [100, 50, 100],
            tag: data.tag || 'visionlab-notification',
            renotify: true,
            data: {
                url: data.url || '/dashboard',
                dateOfArrival: Date.now(),
            },
            actions: data.actions || [
                { action: 'open', title: 'Open' },
                { action: 'dismiss', title: 'Dismiss' },
            ],
        };
        event.waitUntil(
            self.registration.showNotification(data.title || 'VisionLab', options)
        );
    }
});

// Notification Click Handler
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    if (event.action === 'dismiss') return;

    const urlToOpen = event.notification.data?.url || '/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function(clientList) {
                // If a VisionLab window is already open, focus it and navigate
                for (const client of clientList) {
                    if (client.url.includes(self.location.origin) && 'focus' in client) {
                        client.focus();
                        client.navigate(urlToOpen);
                        return;
                    }
                }
                // Otherwise open a new window
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});
