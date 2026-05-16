const vscode = require('vscode');

function activate(context) {
    let disposable = vscode.commands.registerCommand('VisionLab.showPatchPreview', function (patchData) {
        if (!patchData) {
            patchData = { file_path: 'Demo', diff: '+ Demo addition\n- Demo subtraction' };
        }

        const panel = vscode.window.createWebviewPanel(
            'patchPreview',
            'AI Patch Review',
            vscode.ViewColumn.Two,
            { enableScripts: true }
        );

        panel.webview.html = getWebviewContent(patchData);

        panel.webview.onDidReceiveMessage(
            async message => {
                const apiToken = process.env.VisionLab_API_TOKEN || '';
                const apiUrl = process.env.VisionLab_API_URL || 'http://localhost/api';

                switch (message.command) {
                    case 'approve':
                        try {
                            if (patchData.patch_id) {
                                await fetch(`${apiUrl}/ai/patches/${patchData.patch_id}/approve`, {
                                    method: 'POST',
                                    headers: { 'Authorization': `Bearer ${apiToken}` }
                                });
                            }
                            vscode.window.showInformationMessage('Patch Approved and applied to workspace!');
                            // Refresh the active editor if it's the same file to show the changes
                            vscode.commands.executeCommand('workbench.action.files.revert');
                        } catch (e) {
                            vscode.window.showErrorMessage('Failed to apply patch: ' + e.message);
                        }
                        panel.dispose();
                        break;
                    case 'reject':
                        try {
                            if (patchData.patch_id) {
                                await fetch(`${apiUrl}/ai/patches/${patchData.patch_id}/reject`, {
                                    method: 'POST',
                                    headers: { 'Authorization': `Bearer ${apiToken}` }
                                });
                            }
                            vscode.window.showInformationMessage('Patch Rejected!');
                        } catch (e) { }
                        panel.dispose();
                        break;
                }
            },
            undefined,
            context.subscriptions
        );
    });

    context.subscriptions.push(disposable);
}

function getWebviewContent(patchData) {
    const diff = patchData?.diff || "No diff provided.";
    return `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #0a0a0a; color: #fff; padding: 20px; }
        .diff-container { background: #111; padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); overflow-x: auto; font-family: 'JetBrains Mono', Consolas, monospace; font-size: 13px; line-height: 1.5; }
        .diff-add { color: #10b981; background: rgba(16,185,129,0.1); display: block; padding: 0 4px; }
        .diff-remove { color: #ef4444; background: rgba(239,68,68,0.1); display: block; padding: 0 4px; }
        .btn { padding: 12px 20px; margin-right: 12px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; transition: all 0.2s; font-size: 13px; }
        .btn-approve { background: #F97316; color: white; box-shadow: 0 0 15px rgba(249,115,22,0.3); }
        .btn-approve:hover { background: #ea580c; box-shadow: 0 0 20px rgba(249,115,22,0.5); }
        .btn-reject { background: transparent; color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }
        .btn-reject:hover { background: rgba(239,68,68,0.1); }
        h2 { font-size: 18px; margin-bottom: 5px; }
        p { color: #9ca3af; font-size: 13px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Review Proposed Patch</h2>
    <p>Target File: <code style="color:#F97316; background:rgba(249,115,22,0.1); padding:2px 6px; border-radius:4px;">${patchData?.file_path || 'Unknown'}</code></p>
    
    <div class="diff-container">
        ${formatDiff(diff)}
    </div>

    <div style="margin-top: 24px; display: flex;">
        <button class="btn btn-approve" onclick="postMsg('approve')">Approve & Apply</button>
        <button class="btn btn-reject" onclick="postMsg('reject')">Reject</button>
    </div>
    
    <script>
        const vscode = acquireVsCodeApi();
        function postMsg(cmd) {
            vscode.postMessage({ command: cmd });
        }
    </script>
</body>
</html>`;
}

function formatDiff(diffStr) {
    return diffStr.split('\\n').map(line => {
        let safeLine = line.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        if (line.startsWith('+')) return \`<span class="diff-add">\${safeLine}</span>\`;
        if (line.startsWith('-')) return \`<span class="diff-remove">\${safeLine}</span>\`;
        return \`<span>\${safeLine}</span>\`;
    }).join('');
}

exports.activate = activate;
