"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.activate = activate;
exports.deactivate = deactivate;
const vscode = require("vscode");
const RealtimeManager_1 = require("./managers/RealtimeManager");
const DocumentSync_1 = require("./managers/DocumentSync");
const CursorSync_1 = require("./managers/CursorSync");
const VideoPanel_1 = require("./panels/VideoPanel");
const PatchReviewer_1 = require("./panels/PatchReviewer");
function activate(context) {
    console.log('VisionLab Collaboration Agent is now active.');
    const realtimeManager = new RealtimeManager_1.RealtimeManager();
    const documentSync = new DocumentSync_1.DocumentSync(realtimeManager);
    const cursorSync = new CursorSync_1.CursorSync(realtimeManager);
    const patchReviewer = new PatchReviewer_1.PatchReviewer(context);
    // Command to start collaboration
    let startCollabCmd = vscode.commands.registerCommand('visionlab.startCollab', () => {
        realtimeManager.connect();
        documentSync.initialize();
        cursorSync.initialize();
        vscode.window.showInformationMessage('VisionLab: Collaboration session started.');
    });
    // Command to open Video panel
    let openVideoCmd = vscode.commands.registerCommand('visionlab.openVideo', (payload) => {
        if (payload) {
            VideoPanel_1.VideoPanel.createOrShow(context.extensionUri, payload.room_name, payload.jitsi_domain, payload.jwt);
        }
        else {
            vscode.window.showErrorMessage('No video call data provided.');
        }
    });
    let closeVideoCmd = vscode.commands.registerCommand('visionlab.closeVideo', () => {
        if (VideoPanel_1.VideoPanel.currentPanel) {
            VideoPanel_1.VideoPanel.currentPanel.dispose();
        }
    });
    // Command to open Patch Reviewer
    let reviewPatchesCmd = vscode.commands.registerCommand('visionlab.reviewPatches', () => {
        patchReviewer.refresh();
        vscode.commands.executeCommand('visionlab.patchReviewerView.focus');
    });
    context.subscriptions.push(startCollabCmd, openVideoCmd, closeVideoCmd, reviewPatchesCmd);
}
function deactivate() { }
//# sourceMappingURL=extension.js.map