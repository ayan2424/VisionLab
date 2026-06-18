import * as vscode from 'vscode';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

export class RealtimeManager {
    private isConnected: boolean = false;
    private echo: any;
    private channel: any;
    private currentUserId: number = 0;

    public connect() {
        if (this.isConnected) return;
        
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
        global.Pusher = Pusher;

        this.echo = new Echo({
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

        this.channel.here((users: any[]) => {
            this.isConnected = true;
            vscode.window.setStatusBarMessage(`$(broadcast) VisionLab: Collab Connected (${users.length} active)`, 5000);
        })
        .joining((user: any) => {
            vscode.window.showInformationMessage(`${user.name} joined the workspace.`);
        })
        .leaving((user: any) => {
            vscode.window.showInformationMessage(`${user.name} left the workspace.`);
        })
        .listen('ChatMessageSent', (e: any) => {
            if (e.user_id !== this.currentUserId) {
                vscode.window.showInformationMessage(`[${e.user_name}]: ${e.message}`);
            }
        })
        .listen('.video.started', async (e: any) => {
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
            } catch (err) {
                console.error('VisionLab: Failed to fetch JWT for video call', err);
            }
        })
        .listen('.video.ended', (e: any) => {
            vscode.window.showInformationMessage('The video call has ended.');
            vscode.commands.executeCommand('visionlab.closeVideo');
        });
    }

    public disconnect() {
        if (this.echo) {
            this.echo.disconnect();
        }
        this.isConnected = false;
        console.log("Disconnected from VisionLab Reverb.");
    }

    public broadcast(event: string, payload: any) {
        if (!this.isConnected || !this.channel) return;
        this.channel.whisper(event, payload);
    }
    
    public listenForWhisper(event: string, callback: Function) {
        if (!this.channel) return;
        this.channel.listenForWhisper(event, callback);
    }
    
    public getCurrentUserId() {
        return this.currentUserId;
    }
}
