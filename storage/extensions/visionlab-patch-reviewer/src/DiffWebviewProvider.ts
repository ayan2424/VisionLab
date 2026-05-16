import * as vscode from 'vscode';

export class DiffWebviewProvider implements vscode.WebviewViewProvider {
    public static readonly viewType = 'visionlab.patchReviewer';
    private _view?: vscode.WebviewView;

    constructor(
        private readonly _extensionUri: vscode.Uri,
        private readonly _apiUrl: string,
        private readonly _token: string
    ) { }

    public resolveWebviewView(
        webviewView: vscode.WebviewView,
        context: vscode.WebviewViewResolveContext,
        _token: vscode.CancellationToken,
    ) {
        this._view = webviewView;

        webviewView.webview.options = {
            enableScripts: true,
            localResourceRoots: [this._extensionUri]
        };

        webviewView.webview.html = this._getHtmlForWebview();

        webviewView.webview.onDidReceiveMessage(async data => {
            switch (data.type) {
                case 'approve':
                    await this.handleDecision(data.patchId, 'approve');
                    break;
                case 'reject':
                    await this.handleDecision(data.patchId, 'reject');
                    break;
            }
        });
    }

    public loadPatch(patchData: any) {
        if (this._view) {
            this._view.webview.postMessage({ type: 'loadPatch', data: patchData });
            this._view.show?.(true);
        }
    }

    private async handleDecision(patchId: number, action: 'approve' | 'reject') {
        try {
            const url = `${this._apiUrl}/api/ai/patches/${patchId}/${action}`;
            
            // Note: Since node fetch isn't built into standard VS Code environment easily without polyfills,
            // we will send a message back to the Webview to use browser fetch, 
            // OR use a lightweight HTTP library. Since we're in the extension host, we can use built-in https/http.
            // But doing it via webview is easiest.
            if (this._view) {
                this._view.webview.postMessage({ 
                    type: 'executeApi', 
                    url: url, 
                    token: this._token 
                });
            }
        } catch (error: any) {
            vscode.window.showErrorMessage(`Failed to ${action} patch: ${error.message}`);
        }
    }

    private _getHtmlForWebview() {
        return `<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>VisionLab Patch Reviewer</title>
                <style>
                    body {
                        font-family: var(--vscode-font-family);
                        color: var(--vscode-editor-foreground);
                        background-color: var(--vscode-editor-background);
                        padding: 10px;
                        margin: 0;
                        display: flex;
                        flex-direction: column;
                        height: 100vh;
                        box-sizing: border-box;
                    }
                    .header {
                        padding-bottom: 10px;
                        border-bottom: 1px solid var(--vscode-panel-border);
                        margin-bottom: 10px;
                    }
                    .file-name {
                        font-weight: bold;
                        font-size: 1.1em;
                        color: #a855f7; /* Violet */
                    }
                    .diff-container {
                        flex: 1;
                        overflow: auto;
                        display: flex;
                        gap: 10px;
                        font-family: var(--vscode-editor-font-family);
                        font-size: var(--vscode-editor-font-size);
                        background: #111;
                        border-radius: 4px;
                        border: 1px solid var(--vscode-panel-border);
                    }
                    .diff-pane {
                        flex: 1;
                        overflow: auto;
                        padding: 10px;
                        white-space: pre-wrap;
                        word-break: break-all;
                    }
                    .diff-pane.original {
                        border-right: 1px solid var(--vscode-panel-border);
                        background: rgba(255, 0, 0, 0.05);
                    }
                    .diff-pane.patched {
                        background: rgba(0, 255, 0, 0.05);
                    }
                    .actions {
                        display: flex;
                        gap: 10px;
                        padding-top: 10px;
                        margin-top: 10px;
                        border-top: 1px solid var(--vscode-panel-border);
                    }
                    button {
                        padding: 8px 16px;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                        font-weight: bold;
                        color: white;
                        flex: 1;
                    }
                    .btn-approve { background-color: #10b981; }
                    .btn-approve:hover { background-color: #059669; }
                    .btn-reject { background-color: #ef4444; }
                    .btn-reject:hover { background-color: #dc2626; }
                    
                    #empty-state {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        height: 100%;
                        color: var(--vscode-descriptionForeground);
                    }
                    #patch-view { display: none; height: 100%; flex-direction: column; }
                </style>
            </head>
            <body>
                <div id="empty-state">Waiting for AI patches...</div>
                
                <div id="patch-view">
                    <div class="header">
                        <div class="file-name" id="file-name">file.php</div>
                        <div style="font-size: 0.8em; color: var(--vscode-descriptionForeground);">Review AI generated modifications</div>
                    </div>
                    
                    <div class="diff-container">
                        <div class="diff-pane original" id="original-content"></div>
                        <div class="diff-pane patched" id="patched-content"></div>
                    </div>
                    
                    <div class="actions">
                        <button class="btn-reject" id="btn-reject">Reject & Rollback</button>
                        <button class="btn-approve" id="btn-approve">Approve & Apply</button>
                    </div>
                </div>

                <script>
                    const vscode = acquireVsCodeApi();
                    let currentPatchId = null;

                    document.getElementById('btn-approve').addEventListener('click', () => {
                        if (currentPatchId) {
                            vscode.postMessage({ type: 'approve', patchId: currentPatchId });
                            document.getElementById('patch-view').style.display = 'none';
                            document.getElementById('empty-state').style.display = 'flex';
                        }
                    });

                    document.getElementById('btn-reject').addEventListener('click', () => {
                        if (currentPatchId) {
                            vscode.postMessage({ type: 'reject', patchId: currentPatchId });
                            document.getElementById('patch-view').style.display = 'none';
                            document.getElementById('empty-state').style.display = 'flex';
                        }
                    });

                    window.addEventListener('message', event => {
                        const message = event.data;
                        
                        if (message.type === 'loadPatch') {
                            const data = message.data;
                            currentPatchId = data.patch_id;
                            
                            document.getElementById('empty-state').style.display = 'none';
                            document.getElementById('patch-view').style.display = 'flex';
                            
                            document.getElementById('file-name').innerText = data.file_path;
                            document.getElementById('original-content').textContent = data.original_content;
                            document.getElementById('patched-content').textContent = data.patched_content;
                        }
                        
                        if (message.type === 'executeApi') {
                            fetch(message.url, {
                                method: 'POST',
                                headers: {
                                    'Authorization': 'Bearer ' + message.token,
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                // Notification handled by backend or just fire and forget
                            })
                            .catch(err => console.error(err));
                        }
                    });
                </script>
            </body>
            </html>`;
    }
}
