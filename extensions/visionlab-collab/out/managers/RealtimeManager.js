"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.RealtimeManager = void 0;
const vscode = require("vscode");
const laravel_echo_1 = require("laravel-echo");
const pusher_js_1 = require("pusher-js");
class RealtimeManager {
    isConnected = false;
    echo;
    channel;
    currentUserId = 0;
    connect() {
        if (this.isConnected)
            return;
        // Grab values from environment injected by code-server run
        const token = process.env.VISIONCODE_API_TOKEN || '';
        const workspaceId = process.env.VISIONCODE_WORKSPACE_ID || '';
        const apiUrl = process.env.VISIONCODE_API_URL || 'http://localhost:8000/api';
        this.currentUserId = parseInt(process.env.VISIONCODE_USER_ID || '0');
        if (!token || !workspaceId) {
            vscode.window.showErrorMessage('VisionLab: Missing auth environment variables for collaboration.');
            return;
        }
        const authUrl = apiUrl.replace('/api', '') + '/broadcasting/auth';
        const host = apiUrl.includes('localhost') ? 'localhost' : new URL(apiUrl).hostname;
        // @ts-ignore
        global.Pusher = pusher_js_1.default;
        this.echo = new laravel_echo_1.default({
            broadcaster: 'reverb',
            key: 'visioncode-key',
            wsHost: host,
            wsPort: 8080,
            wssPort: 443,
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
            disableStats: true,
            authEndpoint: authUrl,
            auth: {
                headers: {
                    Authorization: 'Bearer ' + token,
                    Accept: 'application/json'
                }
            }
        });
        const roomSlug = `ws-${workspaceId}`;
        this.channel = this.echo.join(`collab.${roomSlug}`);
        this.channel.here((users) => {
            this.isConnected = true;
            vscode.window.setStatusBarMessage(`$(broadcast) VisionLab: Collab Connected (${users.length} active)`, 5000);
        })
            .joining((user) => {
            vscode.window.showInformationMessage(`${user.name} joined the workspace.`);
        })
            .leaving((user) => {
            vscode.window.showInformationMessage(`${user.name} left the workspace.`);
        })
            .listen('ChatMessageSent', (e) => {
            if (e.user_id !== this.currentUserId) {
                vscode.window.showInformationMessage(`[${e.user_name}]: ${e.message}`);
            }
        })
            .listen('.video.started', async (e) => {
            if (e.starterId !== this.currentUserId) {
                vscode.window.showInformationMessage(`${e.starterName} started a video call.`);
            }
            // Fetch our own JWT from the backend
            const token = process.env.VISIONCODE_API_TOKEN || '';
            const apiUrl = process.env.VISIONCODE_API_URL || 'http://localhost:8000/api';
            const workspaceId = process.env.VISIONCODE_WORKSPACE_ID || '';
            try {
                // To call /status we need the slug. We can assume e.workspaceSlug is available (it is in the event).
                const res = await fetch(`${apiUrl}/workspace/${e.workspaceSlug}/video/start`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.active) {
                    vscode.commands.executeCommand('visionlab.openVideo', {
                        room_name: data.room_name,
                        jitsi_domain: data.jitsi_domain,
                        jwt: data.jwt
                    });
                }
            }
            catch (err) {
                console.error('VisionLab: Failed to fetch JWT for video call', err);
            }
        })
            .listen('.video.ended', (e) => {
            vscode.window.showInformationMessage('The video call has ended.');
            vscode.commands.executeCommand('visionlab.closeVideo');
        });
    }
    disconnect() {
        if (this.echo) {
            this.echo.disconnect();
        }
        this.isConnected = false;
        console.log("Disconnected from VisionLab Reverb.");
    }
    broadcast(event, payload) {
        if (!this.isConnected || !this.channel)
            return;
        this.channel.whisper(event, payload);
    }
    listenForWhisper(event, callback) {
        if (!this.channel)
            return;
        this.channel.listenForWhisper(event, callback);
    }
    getCurrentUserId() {
        return this.currentUserId;
    }
}
exports.RealtimeManager = RealtimeManager;
//# sourceMappingURL=RealtimeManager.js.map