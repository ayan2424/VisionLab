import * as vscode from 'vscode';
import { RealtimeManager } from './managers/RealtimeManager';
import { DocumentSync } from './managers/DocumentSync';
import { CursorSync } from './managers/CursorSync';
import { VideoPanel } from './panels/VideoPanel';
import { PatchReviewer } from './panels/PatchReviewer';

export function activate(context: vscode.ExtensionContext) {
    console.log('VisionLab Collaboration Agent is now active.');

    const realtimeManager = new RealtimeManager();
    const documentSync = new DocumentSync(realtimeManager);
    const cursorSync = new CursorSync(realtimeManager);
    const patchReviewer = new PatchReviewer(context);

    // Command to start collaboration
    let startCollabCmd = vscode.commands.registerCommand('visionlab.startCollab', () => {
        realtimeManager.connect();
        documentSync.initialize();
        cursorSync.initialize();
        vscode.window.showInformationMessage('VisionLab: Collaboration session started.');
    });

    // Command to open Video panel
    let openVideoCmd = vscode.commands.registerCommand('visionlab.openVideo', (payload?: any) => {
        if (payload) {
            VideoPanel.createOrShow(context.extensionUri, payload.room_name, payload.jitsi_domain, payload.jwt);
        } else {
            vscode.window.showErrorMessage('No video call data provided.');
        }
    });

    let closeVideoCmd = vscode.commands.registerCommand('visionlab.closeVideo', () => {
        if (VideoPanel.currentPanel) {
            VideoPanel.currentPanel.dispose();
        }
    });

    // Command to open Patch Reviewer
    let reviewPatchesCmd = vscode.commands.registerCommand('visionlab.reviewPatches', () => {
        patchReviewer.refresh();
        vscode.commands.executeCommand('visionlab.patchReviewerView.focus');
    });

    context.subscriptions.push(startCollabCmd, openVideoCmd, closeVideoCmd, reviewPatchesCmd);
}

export function deactivate() {}
