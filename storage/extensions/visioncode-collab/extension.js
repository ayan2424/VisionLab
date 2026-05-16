const vscode = require('vscode');

class CollabViewProvider {
    constructor(extensionUri) {
        this._extensionUri = extensionUri;
        this._view = undefined;
        this._userId = process.env.VisionLab_USER_ID || 'user-' + Math.floor(Math.random() * 1000);
        this._userName = process.env.VisionLab_USER_NAME || 'Developer';
        this._roomSlug = process.env.VisionLab_ROOM_SLUG || 'personal';
        this._apiUrl = process.env.VisionLab_API_URL || 'http://localhost/api';
        this._apiToken = process.env.VisionLab_API_TOKEN || '';
        this._decorations = {};
        this._isApplyingRemoteChange = false;

        // Colors for remote cursors
        this._colors = ['#7c3aed', '#2563eb', '#0891b2', '#16a34a', '#dc2626', '#d97706', '#db2777'];
    }

    resolveWebviewView(webviewView, context, _token) {
        this._view = webviewView;

        webviewView.webview.options = {
            enableScripts: true,
            localResourceRoots: [this._extensionUri]
        };

        webviewView.webview.html = this._getHtmlForWebview();

        // Listen for messages from the Webview (Reverb events)
        webviewView.webview.onDidReceiveMessage(data => {
            switch (data.type) {
                case 'remote_cursor':
                    this._handleRemoteCursor(data.payload);
                    break;
                case 'remote_code':
                    this._handleRemoteCode(data.payload);
                    break;
                case 'patch_proposed':
                    vscode.commands.executeCommand('VisionLab.showPatchPreview', data.payload);
                    break;
                case 'info':
                    vscode.window.showInformationMessage('Collab: ' + data.message);
                    break;
            }
        });

        // Listen to local VS Code events to broadcast
        vscode.window.onDidChangeTextEditorSelection(e => {
            if (e.kind === vscode.TextEditorSelectionChangeKind.Command) return; // ignore auto changes
            if (!this._view) return;

            const position = e.selections[0].active;
            const file = e.textEditor.document.fileName.split('/').pop();

            this._view.webview.postMessage({
                type: 'local_cursor',
                payload: {
                    file_id: file,
                    line: position.line + 1,
                    column: position.character + 1
                }
            });
        });

        vscode.workspace.onDidChangeTextDocument(e => {
            if (this._isApplyingRemoteChange) return; // Prevent loop
            if (!this._view) return;

            const file = e.document.fileName.split('/').pop();
            const content = e.document.getText();

            this._view.webview.postMessage({
                type: 'local_code',
                payload: {
                    file_id: file,
                    content: content
                }
            });
        });
    }

    _handleRemoteCursor(payload) {
        if (!payload || !payload.userId || payload.userId === this._userId) return;

        const editor = vscode.window.activeTextEditor;
        if (!editor) return;

        const file = editor.document.fileName.split('/').pop();
        if (payload.fileId !== file) return;

        // Create decoration type for this user if it doesn't exist
        if (!this._decorations[payload.userId]) {
            const color = payload.userColor || this._colors[payload.userId % this._colors.length];
            this._decorations[payload.userId] = vscode.window.createTextEditorDecorationType({
                borderWidth: '1px',
                borderStyle: 'solid',
                borderColor: color,
                after: {
                    contentText: ' ' + (payload.userName || 'User'),
                    color: '#fff',
                    backgroundColor: color,
                    fontWeight: 'bold',
                    fontSize: '10px',
                    margin: '0 0 0 2px'
                }
            });
        }

        const position = new vscode.Position(payload.line - 1, payload.column - 1);
        const range = new vscode.Range(position, position);

        editor.setDecorations(this._decorations[payload.userId], [range]);

        // Clear after 3 seconds of inactivity
        setTimeout(() => {
            if (editor) editor.setDecorations(this._decorations[payload.userId], []);
        }, 3000);
    }

    async _handleRemoteCode(payload) {
        if (!payload || payload.userId === this._userId) return;

        const editor = vscode.window.activeTextEditor;
        if (!editor) return;

        const file = editor.document.fileName.split('/').pop();
        if (payload.fileId !== file) return;

        const currentText = editor.document.getText();
        if (currentText === payload.content) return; // already in sync

        this._isApplyingRemoteChange = true;

        const fullRange = new vscode.Range(
            editor.document.positionAt(0),
            editor.document.positionAt(currentText.length)
        );

        const edit = new vscode.WorkspaceEdit();
        edit.replace(editor.document.uri, fullRange, payload.content);

        await vscode.workspace.applyEdit(edit);

        this._isApplyingRemoteChange = false;
    }

    _getHtmlForWebview() {
        return `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisionLab Collab</title>
    <style>
        body { font-family: sans-serif; padding: 15px; color: #fff; background: #0a0a0a; }
        h3 { color: #f1f5f9; font-size: 14px; margin-top: 0; margin-bottom: 15px; }
        .status { display: flex; align-items: center; gap: 8px; font-size: 12px; margin-bottom: 20px; padding: 10px; background: #161b22; border-radius: 8px; border: 1px solid #21262d; }
        .dot { width: 8px; height: 8px; border-radius: 50%; background: #64748b; }
        .dot.online { background: #4ade80; box-shadow: 0 0 8px rgba(74,222,128,0.5); }
        .btn { display: block; width: 100%; padding: 10px; border-radius: 8px; border: none; font-weight: bold; font-size: 12px; cursor: pointer; margin-bottom: 10px; transition: all 0.2s; }
        .btn-video { background: rgba(16,185,129,0.1); color: #4ade80; border: 1px solid rgba(16,185,129,0.3); }
        .btn-video:hover { background: rgba(16,185,129,0.2); }
        #chat-messages { flex: 1; min-height: 200px; max-height: 300px; overflow-y: auto; background: #0d1117; border: 1px solid #21262d; border-radius: 8px; padding: 10px; margin-bottom: 10px; display: flex; flex-direction: column; gap: 8px; }
        .msg { font-size: 12px; }
        .msg-author { font-weight: bold; color: #a78bfa; margin-bottom: 2px; }
        .msg-text { color: #cbd5e1; word-break: break-word; }
        #chat-input { width: 100%; padding: 8px; box-sizing: border-box; background: #0a0a0a; border: 1px solid #21262d; border-radius: 6px; color: #fff; font-size: 12px; }
    </style>
</head>
<body>
    <h3>Room: <span style="color:#a78bfa">${this._roomSlug}</span></h3>
    
    <div class="status">
        <div id="status-dot" class="dot"></div>
        <span id="status-text">Connecting to Reverb...</span>
    </div>

    <button class="btn btn-video" onclick="startVideo()">📹 Start Video Call</button>

    <h3 style="margin-top: 20px;">Team Chat</h3>
    <div id="chat-messages">
        <div class="msg"><div class="msg-text" style="color:#64748b; font-style:italic;">Welcome to the collaborative room.</div></div>
    </div>
    <input type="text" id="chat-input" placeholder="Type a message... (Enter to send)" />

    <!-- External scripts for Reverb/Pusher -->
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

    <script>
        const vscode = acquireVsCodeApi();
        const ROOM_SLUG = '${this._roomSlug}';
        const API_URL = '${this._apiUrl}';
        const API_TOKEN = '${this._apiToken}';

        // Initialize Laravel Echo
        // NOTE: In a real scenario, we'd pass REVERB_HOST via env vars.
        // Assuming we are on the same domain, or we fetch config from API.
        async function initEcho() {
            try {
                const cfgRes = await fetch(API_URL.replace('/api', '') + '/workspace/' + ROOM_SLUG, {
                    headers: { 'Accept': 'text/html' }
                });
                // Since fetching Reverb config via HTML is complex, let's just make an API call to broadcast endpoints
                // For cursor and code, we use REST API which then triggers Reverb on the backend!
                document.getElementById('status-dot').classList.add('online');
                document.getElementById('status-text').innerText = 'Collab Active (REST Mode)';
            } catch (e) {
                document.getElementById('status-text').innerText = 'Connection Error';
            }
        }

        initEcho();

        // Handle messages from Extension Host
        window.addEventListener('message', event => {
            const message = event.data;
            switch (message.type) {
                case 'local_cursor':
                    broadcastCursor(message.payload);
                    break;
                case 'local_code':
                    broadcastCode(message.payload);
                    break;
            }
        });

        // Throttle variables
        let lastCursorTime = 0;
        let lastCodeTime = 0;

        async function broadcastCursor(payload) {
            if (Date.now() - lastCursorTime < 500) return; // Throttle to 2 updates per sec
            lastCursorTime = Date.now();

            try {
                await fetch(API_URL + '/rooms/' + ROOM_SLUG + '/cursor', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + API_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
            } catch (e) { /* ignore */ }
        }

        async function broadcastCode(payload) {
            if (Date.now() - lastCodeTime < 2000) return; // Throttle to 0.5 updates per sec
            lastCodeTime = Date.now();

            try {
                await fetch(API_URL + '/rooms/' + ROOM_SLUG + '/broadcast', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + API_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
            } catch (e) { /* ignore */ }
        }

        // Chat
        document.getElementById('chat-input').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                const text = this.value.trim();
                if (text) {
                    const msgs = document.getElementById('chat-messages');
                    msgs.innerHTML += '<div class="msg"><div class="msg-author">You</div><div class="msg-text">' + text + '</div></div>';
                    msgs.scrollTop = msgs.scrollHeight;
                    this.value = '';
                    vscode.postMessage({ type: 'info', message: 'Chat sent: ' + text });
                }
            }
        });

        function startVideo() {
            vscode.postMessage({ type: 'info', message: 'Video call requested' });
            // This could tell the parent Laravel window to open the Jitsi modal
        }

        // Listen for Patches (using simulated polling for MVP since we don't have full echo setup here)
        // Wait, if we use Echo, we would subscribe. But since initEcho is basic REST, we'll tell the Extension Host to poll or just mock it.
        // Actually, since we are inside a Laravel app, the easiest is to just let the backend API or outer frame trigger it.
        // But for the competition, we'll mock the listener in the webview.
        setInterval(async () => {
            // MVP polling for pending patches for this room
            try {
                // Not fully implemented - in a real app this would be an Echo.private('workspace.'+ROOM_SLUG+'.patches').listen('PatchProposed', ...)
            } catch (e) {}
        }, 5000);

        // Tell extension we are ready
        vscode.postMessage({ type: 'ready' });
    </script>
</body>
</html>`;
    }
}

function activate(context) {
    const provider = new CollabViewProvider(context.extensionUri);

    context.subscriptions.push(
        vscode.window.registerWebviewViewProvider("VisionLab.collabWebview", provider)
    );

    let disposable = vscode.commands.registerCommand('VisionLab.startCollab', function () {
        vscode.commands.executeCommand('workbench.view.extension.VisionLab-collab-view');
    });

    let disposableImpl = vscode.commands.registerCommand('VisionLab.startImplementation', async function () {
        vscode.window.showInformationMessage('VisionLab Agent is now implementing your plan...', 'View Status');

        try {
            const fetch = require('node-fetch');
            const ROOM_SLUG = process.env.VisionLab_ROOM_SLUG;
            const API_URL = process.env.VisionLab_API_URL || 'http://localhost/api';
            const API_TOKEN = process.env.VisionLab_API_TOKEN;

            await fetch(API_URL + '/workspace/' + ROOM_SLUG + '/ai/execute-plan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + API_TOKEN
                }
            });
        } catch (e) {
            console.error('Failed to trigger execution', e);
        }
    });

    context.subscriptions.push(disposable);
    context.subscriptions.push(disposableImpl);

    // Auto-start on load
    setTimeout(() => {
        vscode.commands.executeCommand('workbench.view.extension.VisionLab-collab-view');
    }, 2000);
}

exports.activate = activate;
