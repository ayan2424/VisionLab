"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.CursorSync = void 0;
const vscode = require("vscode");
class CursorSync {
    realtime;
    decorations = new Map();
    activeCursors = new Map();
    constructor(realtime) {
        this.realtime = realtime;
    }
    initialize() {
        this.realtime.listenForWhisper('client-CursorMoved', (data) => {
            const { file, selections, user_id, color, name } = data;
            // Generate a unique decoration for this user if it doesn't exist
            if (!this.decorations.has(user_id)) {
                const dec = vscode.window.createTextEditorDecorationType({
                    borderWidth: '0 0 0 2px',
                    borderStyle: 'solid',
                    borderColor: color || '#f97316',
                    after: {
                        contentText: name,
                        color: '#fff',
                        backgroundColor: color || '#f97316',
                        fontWeight: 'bold',
                        margin: '0 0 0 2px'
                    }
                });
                this.decorations.set(user_id, dec);
            }
            const ranges = selections.map((s) => new vscode.Range(s.start.line, s.start.character, s.end.line, s.end.character));
            this.activeCursors.set(`${file}_${user_id}`, ranges);
            this.applyDecorations(file);
        });
        // Whenever active editor changes, we re-apply cursors
        vscode.window.onDidChangeActiveTextEditor(editor => {
            if (editor && editor.document.uri.scheme === 'file') {
                const relativePath = vscode.workspace.asRelativePath(editor.document.uri);
                this.applyDecorations(relativePath);
            }
        });
    }
    applyDecorations(file) {
        const editor = vscode.window.visibleTextEditors.find(e => vscode.workspace.asRelativePath(e.document.uri) === file);
        if (!editor)
            return;
        this.decorations.forEach((decoration, userId) => {
            const ranges = this.activeCursors.get(`${file}_${userId}`);
            if (ranges) {
                editor.setDecorations(decoration, ranges);
            }
        });
    }
    dispose() {
        this.decorations.forEach(dec => dec.dispose());
    }
}
exports.CursorSync = CursorSync;
//# sourceMappingURL=CursorSync.js.map