document.addEventListener('DOMContentLoaded', () => {
    // ── 1. Online/Offline Listeners ──
    window.addEventListener('online', () => {
        if (window.vcToast) window.vcToast('Connection Restored', 'success');
        document.body.classList.remove('is-offline');
    });

    window.addEventListener('offline', () => {
        if (window.vcToast) window.vcToast('You are offline. Some features may be unavailable.', 'error');
        document.body.classList.add('is-offline');
    });

    // ── 2. Service Worker Registration ──
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/serviceworker.js')
            .then(registration => {
                console.log('SW Registered:', registration);
                // Try to subscribe to push notifications
                if (document.querySelector('meta[name="auth-user-id"]')) {
                    setupPushNotifications(registration);
                }
            })
            .catch(error => {
                console.log('SW Registration failed:', error);
            });
    }

    // ── 3. Push Notifications Setup ──
    function setupPushNotifications(registration) {
        const vapidPublicKey = document.querySelector('meta[name="vapid-public-key"]')?.content;
        if (!vapidPublicKey) return;

        registration.pushManager.getSubscription().then(subscription => {
            if (subscription) {
                // Already subscribed
                return;
            }

            // Ask for permission and subscribe
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    subscribeUserToPush(registration, vapidPublicKey);
                }
            });
        });
    }

    function subscribeUserToPush(registration, vapidPublicKey) {
        const convertedVapidKey = urlBase64ToUint8Array(vapidPublicKey);
        
        registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: convertedVapidKey
        }).then(subscription => {
            // Send subscription to server
            fetch('/api/push-subscriptions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(subscription)
            }).then(res => {
                if(res.ok) console.log('Successfully subscribed to push notifications');
            });
        }).catch(err => {
            console.error('Failed to subscribe to push notifications', err);
        });
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    // ── 4. PWA Install Prompt ──
    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent the mini-infobar from appearing on mobile
        e.preventDefault();
        deferredPrompt = e;
        
        // Show an install banner if we have a container for it
        const installBtn = document.getElementById('install-pwa-btn');
        if (installBtn) {
            installBtn.style.display = 'block';
            installBtn.addEventListener('click', () => {
                installBtn.style.display = 'none';
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    deferredPrompt = null;
                });
            });
        }
    });
});
