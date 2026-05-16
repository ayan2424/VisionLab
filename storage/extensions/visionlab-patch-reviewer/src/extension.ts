import * as vscode from 'vscode';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { DiffWebviewProvider } from './DiffWebviewProvider';

// Provide Pusher globally for Echo
(global as any).Pusher = Pusher;

export function activate(context: vscode.ExtensionContext) {
    console.log('VisionLab Patch Reviewer is now active.');

    // Attempt to get environment variables (these should be injected into the container by CodeServerManager)
    const hostBaseUrl = process.env.APP_URL || 'http://host.docker.internal:8000';
    const workspaceId = process.env.WORKSPACE_ID;
    const userToken = process.env.WORKSPACE_TOKEN;
    const wsHost = process.env.REVERB_HOST || '127.0.0.1';
    const wsPort = process.env.REVERB_PORT || '8080';
    const wsScheme = process.env.REVERB_SCHEME || 'http';

    if (!workspaceId || !userToken) {
        console.error('Missing workspace credentials. Patch Reviewer disabled.');
        return;
    }

    const diffProvider = new DiffWebviewProvider(context.extensionUri, hostBaseUrl, userToken);
    
    context.subscriptions.push(
        vscode.window.registerWebviewViewProvider(DiffWebviewProvider.viewType, diffProvider)
    );

    context.subscriptions.push(
        vscode.commands.registerCommand('visionlab.showPatchPreview', (patchData: any) => {
            // Trigger webview to show
            vscode.commands.executeCommand('visionlab.patchReviewer.focus');
            setTimeout(() => {
                diffProvider.loadPatch(patchData);
            }, 500); // Wait for webview to load
        })
    );

    // Initialize Laravel Echo
    const echo = new Echo({
        broadcaster: 'reverb',
        key: process.env.REVERB_APP_KEY,
        wsHost: wsHost,
        wsPort: wsPort,
        wssPort: wsPort,
        forceTLS: wsScheme === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: `${hostBaseUrl}/broadcasting/auth`,
        auth: {
            headers: {
                Authorization: `Bearer ${userToken}`,
            },
        },
    });

    // Listen on the private workspace channel
    echo.private(`workspace.${workspaceId}.patches`)
        .listen('PatchProposed', (e: any) => {
            vscode.window.showInformationMessage('New AI Patch Proposed!', 'Review').then(selection => {
                if (selection === 'Review') {
                    vscode.commands.executeCommand('visionlab.showPatchPreview', e);
                }
            });
            // Or force it open instantly as per prompt
            vscode.commands.executeCommand('visionlab.showPatchPreview', e);
        });

    // --- VisionGuard AI Forensics Tracking ---
    let humanChars = 0;
    let aiChars = 0;
    let timeSpent = 0;
    
    // Timer to track active seconds
    setInterval(() => {
        timeSpent += 1;
    }, 1000);

    // Track text changes
    context.subscriptions.push(
        vscode.workspace.onDidChangeTextDocument(e => {
            if (e.document.uri.scheme !== 'file') return;
            
            for (const change of e.contentChanges) {
                const textLength = change.text.length;
                
                // Heuristic: If it's a large chunk of text pasted suddenly, it might be AI or paste
                // But specifically for Continue/Agent patches, they are applied programmatically and can be huge
                if (textLength > 50) {
                    aiChars += textLength;
                } else {
                    humanChars += textLength;
                }
            }
        })
    );

    // Sync telemetry every 60 seconds
    setInterval(() => {
        if (humanChars > 0 || aiChars > 0 || timeSpent > 0) {
            fetch(`${hostBaseUrl}/api/ai/forensics/sync`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${userToken}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    submission_id: workspaceId, // For MVP, assuming workspace_id = submission_id 
                    human_keystrokes: humanChars,
                    ai_injected_chars: aiChars,
                    time_spent_seconds: timeSpent
                })
            }).then(res => {
                if (res.ok) {
                    // Reset after successful sync
                    humanChars = 0;
                    aiChars = 0;
                    timeSpent = 0;
                }
            }).catch(console.error);
        }
    }, 60000);
}

export function deactivate() {}
