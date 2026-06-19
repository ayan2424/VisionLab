importScripts('https://storage.googleapis.com/workbox-cdn/releases/7.0.0/workbox-sw.js');

if (workbox) {
    console.log('Workbox is loaded');

    // Setup cache names
    workbox.core.setCacheNameDetails({
        prefix: 'visionlab',
        suffix: 'v1'
    });

    // 1. Stale While Revalidate for HTML Pages
    workbox.routing.registerRoute(
        ({request}) => request.mode === 'navigate',
        new workbox.strategies.StaleWhileRevalidate({
            cacheName: 'pages-cache',
            plugins: [
                new workbox.cacheableResponse.CacheableResponsePlugin({
                    statuses: [200],
                }),
            ],
        })
    );

    // 2. Cache First for Static Assets (CSS, JS, Web Workers)
    workbox.routing.registerRoute(
        ({request}) =>
            request.destination === 'style' ||
            request.destination === 'script' ||
            request.destination === 'worker',
        new workbox.strategies.CacheFirst({
            cacheName: 'static-resources',
            plugins: [
                new workbox.expiration.ExpirationPlugin({
                    maxEntries: 50,
                    maxAgeSeconds: 30 * 24 * 60 * 60, // 30 Days
                }),
            ],
        })
    );

    // 3. Cache First for Images
    workbox.routing.registerRoute(
        ({request}) => request.destination === 'image',
        new workbox.strategies.CacheFirst({
            cacheName: 'images',
            plugins: [
                new workbox.expiration.ExpirationPlugin({
                    maxEntries: 60,
                    maxAgeSeconds: 30 * 24 * 60 * 60, // 30 Days
                }),
            ],
        })
    );

    // 4. Network First for API routes (except submissions)
    workbox.routing.registerRoute(
        ({url}) => url.pathname.startsWith('/api/') && !url.pathname.includes('submissions'),
        new workbox.strategies.NetworkFirst({
            cacheName: 'api-cache',
            networkTimeoutSeconds: 5,
            plugins: [
                new workbox.cacheableResponse.CacheableResponsePlugin({
                    statuses: [200],
                }),
            ],
        })
    );

    // 5. Background Sync for Offline Submissions
    const bgSyncPlugin = new workbox.backgroundSync.BackgroundSyncPlugin('offline-submissions', {
        maxRetentionTime: 24 * 60 // Retry for max of 24 Hours
    });

    workbox.routing.registerRoute(
        /\/api\/submissions/,
        new workbox.strategies.NetworkOnly({
            plugins: [bgSyncPlugin]
        }),
        'POST'
    );

} else {
    console.log('Workbox could not be loaded. No Offline support');
}

// ── WebPush Event Listener ──────────────────────────────────────────────
self.addEventListener('push', function (event) {
    if (!event.data) {
        console.log('Push event but no data');
        return;
    }

    try {
        const data = event.data.json();

        const title = data.title || 'VisionLab Notification';
        const options = {
            body: data.body || 'You have a new message.',
            icon: data.icon || '/icon-192.png',
            badge: '/icon-192.png', // Small monochrome icon
            data: data.url || '/',
            vibrate: [200, 100, 200]
        };

        event.waitUntil(self.registration.showNotification(title, options));
    } catch (e) {
        console.error('Error parsing push data', e);
        // Fallback for plain text
        event.waitUntil(
            self.registration.showNotification('VisionLab', {
                body: event.data.text(),
                icon: '/icon-192.png'
            })
        );
    }
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    
    // Open the url from data
    const targetUrl = event.notification.data;
    if (targetUrl) {
        event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
                for (let i = 0; i < windowClients.length; i++) {
                    const client = windowClients[i];
                    if (client.url === targetUrl && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(targetUrl);
                }
            })
        );
    }
});
