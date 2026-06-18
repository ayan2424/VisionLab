"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.PatchReviewer = void 0;
const vscode = require("vscode");
class PatchReviewer {
    _extensionUri;
    _view;
    constructor(_extensionUri) {
        this._extensionUri = _extensionUri;
        vscode.window.registerWebviewViewProvider('visionlab.patchReviewerView', this);
    }
    resolveWebviewView(webviewView, context, _token) {
        this._view = webviewView;
        webviewView.webview.options = { enableScripts: true };
        webviewView.webview.html = this._getHtmlForWebview();
    }
    refresh() {
        if (this._view) {
            this._view.webview.html = this._getHtmlForWebview();
        }
    }
    _getHtmlForWebview() {
        const token = process.env.VISIONCODE_API_TOKEN || '';
        const apiUrl = process.env.VISIONCODE_API_URL || '';
        const workspaceId = process.env.VISIONCODE_WORKSPACE_ID || '';
        return `<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Patch Reviewer</title>
                <style>
                    body { font-family: sans-serif; padding: 10px; color: var(--vscode-foreground); background-color: var(--vscode-editor-background); }
                    .card { border: 1px solid var(--vscode-panel-border); padding: 12px; margin-bottom: 12px; border-radius: 6px; background: var(--vscode-editorWidget-background); }
                    .btn { background: var(--vscode-button-background); color: var(--vscode-button-foreground); border: none; padding: 6px 12px; cursor: pointer; border-radius: 4px; font-weight: 500; margin-right: 8px;}
                    .btn:hover { background: var(--vscode-button-hoverBackground); }
                    .btn-danger { background: var(--vscode-errorForeground); color: white; }
                    .diff-box { background: #1e1e1e; color: #d4d4d4; padding: 8px; border-radius: 4px; font-family: monospace; font-size: 12px; overflow-x: auto; margin: 10px 0; max-height: 200px;}
                    .diff-add { color: #4ade80; }
                    .diff-rm { color: #f87171; }
                </style>
            </head>
            <body>
                <h3 style="display:flex; justify-content:space-between; align-items:center;">
                    Pending AI Patches
                    <button class="btn" onclick="fetchPatches()" style="font-size:11px; padding:4px 8px;">Refresh</button>
                </h3>
                <div id="patches-container">Loading patches...</div>

                <script>
                    const vscode = acquireVsCodeApi();
                    const API_URL = '${apiUrl}';
                    const TOKEN = '${token}';
                    const WS_ID = '${workspaceId}';

                    async function fetchPatches() {
                        const container = document.getElementById('patches-container');
                        if (!API_URL || !TOKEN) {
                            container.innerHTML = 'Missing VisionLab authentication environment.';
                            return;
                        }

                        container.innerHTML = 'Loading patches...';
                        try {
                            const res = await fetch(\`\${API_URL}/ai/patches/pending?workspace_id=\${WS_ID}\`, {
                                headers: { 'Authorization': 'Bearer ' + TOKEN, 'Accept': 'application/json' }
                            });
                            const data = await res.json();
                            
                            if (data.length === 0) {
                                container.innerHTML = '<div style="color:var(--vscode-descriptionForeground);">No pending patches.</div>';
                                return;
                            }

                            container.innerHTML = data.map(patch => {
                                // Simple syntax highlight for diff
                                const formattedDiff = patch.diff.split('\\n').map(line => {
                                    if (line.startsWith('+')) return \`<span class="diff-add">\${line}</span>\`;
                                    if (line.startsWith('-')) return \`<span class="diff-rm">\${line}</span>\`;
                                    return line;
                                }).join('<br/>');

                                return \`
                                <div class="card" id="patch-\${patch.id}">
                                    <strong style="color:var(--vscode-symbolIcon-fileForeground);">\${patch.file_path}</strong><br/>
                                    <div class="diff-box">\${formattedDiff}</div>
                                    <button class="btn" onclick="actionPatch(\${patch.id}, 'approve')">Approve</button>
                                    <button class="btn btn-danger" onclick="actionPatch(\${patch.id}, 'reject')">Reject</button>
                                </div>
                                \`;
                            }).join('');

                        } catch (err) {
                            container.innerHTML = 'Error loading patches.';
                            console.error(err);
                        }
                    }

                    async function actionPatch(id, action) {
                        try {
                            const res = await fetch(\`\${API_URL}/ai/patches/\${id}/\${action}\`, {
                                method: 'POST',
                                headers: { 'Authorization': 'Bearer ' + TOKEN, 'Accept': 'application/json' }
                            });
                            
                            if (res.ok) {
                                document.getElementById('patch-' + id).style.display = 'none';
                            } else {
                                alert('Failed to ' + action + ' patch.');
                            }
                        } catch (err) {
                            alert('Network error.');
                        }
                    }

                    // Initial fetch
                    fetchPatches();
                </script>
            </body>
            </html>`;
    }
}
exports.PatchReviewer = PatchReviewer;
//# sourceMappingURL=PatchReviewer.js.map