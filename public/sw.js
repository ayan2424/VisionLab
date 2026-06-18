importScripts('https://storage.googleapis.com/workbox-cdn/releases/7.0.0/workbox-sw.js');

if (workbox) {
  console.log('Workbox is loaded');

  // Cache static assets (CSS, JS, Web Workers)
  workbox.routing.registerRoute(
    /\.(?:js|css)$/,
    new workbox.strategies.StaleWhileRevalidate({
      cacheName: 'static-resources',
    })
  );

  // Cache images
  workbox.routing.registerRoute(
    /\.(?:png|gif|jpg|jpeg|webp|svg)$/,
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

  // Network First for critical API routes
  workbox.routing.registerRoute(
    /\/api\/.*/,
    new workbox.strategies.NetworkFirst({
      cacheName: 'api-responses',
      networkTimeoutSeconds: 3,
    })
  );

  // Offline fallback for navigation
  workbox.routing.registerRoute(
    ({request}) => request.mode === 'navigate',
    new workbox.strategies.NetworkFirst({
      cacheName: 'pages',
    })
  );

  // Push notifications listener
  self.addEventListener('push', function(event) {
    let data = {};
    if (event.data) {
      data = event.data.json();
    }
    
    const title = data.title || 'VisionLab Update';
    const options = {
      body: data.body || 'You have a new notification.',
      icon: '/icon-192x192.png',
      badge: '/badge-72x72.png',
      data: {
        url: data.url || '/'
      }
    };

    event.waitUntil(self.registration.showNotification(title, options));
  });

  // Notification click listener
  self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
      clients.openWindow(event.notification.data.url)
    );
  });
} else {
  console.log('Workbox could not be loaded. No offline support.');
}
