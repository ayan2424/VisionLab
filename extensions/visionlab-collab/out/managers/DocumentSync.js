"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.DocumentSync = void 0;
const vscode = require("vscode");
class DocumentSync {
    realtime;
    disposable;
    constructor(realtime) {
        this.realtime = realtime;
    }
    initialize() {
        // Prevent echo loop
        let isApplyingNetworkChange = false;
        this.disposable = vscode.workspace.onDidChangeTextDocument(event => {
            if (isApplyingNetworkChange || event.document.uri.scheme !== 'file')
                return;
            // Re-broadcast document change
            this.realtime.broadcast('client-CodeUpdated', {
                file: vscode.workspace.asRelativePath(event.document.uri),
                changes: event.contentChanges
            });
        });
        vscode.window.onDidChangeTextEditorSelection(event => {
            if (event.textEditor.document.uri.scheme !== 'file')
                return;
            this.realtime.broadcast('client-CursorMoved', {
                file: vscode.workspace.asRelativePath(event.textEditor.document.uri),
                selections: event.selections,
                user_id: this.realtime.getCurrentUserId(),
                name: process.env.VISIONCODE_USER_NAME || 'User',
                color: process.env.VISIONCODE_USER_COLOR || '#f97316'
            });
        });
        // Listen for CodeUpdated
        this.realtime.listenForWhisper('client-CodeUpdated', async (data) => {
            isApplyingNetworkChange = true;
            try {
                const workspaces = vscode.workspace.workspaceFolders;
                if (!workspaces)
                    return;
                const uri = vscode.Uri.joinPath(workspaces[0].uri, data.file);
                const edit = new vscode.WorkspaceEdit();
                // Build edits
                data.changes.forEach((change) => {
                    const range = new vscode.Range(change.range[0].line, change.range[0].character, change.range[1].line, change.range[1].character);
                    edit.replace(uri, range, change.text);
                });
                await vscode.workspace.applyEdit(edit);
            }
            catch (e) {
                console.error("DocumentSync apply error:", e);
            }
            finally {
                isApplyingNetworkChange = false;
            }
        });
    }
    dispose() {
        if (this.disposable) {
            this.disposable.dispose();
        }
    }
}
exports.DocumentSync = DocumentSync;
//# sourceMappingURL=DocumentSync.js.map