import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

/**
 * Lazy Echo initialiser — called from workspace.blade.php with runtime
 * PHP config so it works across all Replit / dev / prod domains without
 * a Vite rebuild. Calling multiple times is a no-op.
 */
window.initEcho = function (reverb) {
    if (window.Echo) return window.Echo;

    window.Echo = new Echo({
        broadcaster:       'reverb',
        key:               reverb.key,
        wsHost:            reverb.host,
        wsPort:            reverb.port,
        wssPort:           reverb.port,
        forceTLS:          reverb.scheme === 'https',
        enabledTransports: ['ws', 'wss'],
        disableStats:      true,
        authEndpoint:      '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                'Accept':       'application/json',
            },
        },
    });

    return window.Echo;
};
