import * as vscode from 'vscode';

export class VideoPanel {
    public static currentPanel: VideoPanel | undefined;
    private readonly _panel: vscode.WebviewPanel;

    private constructor(panel: vscode.WebviewPanel, extensionUri: vscode.Uri, roomName: string, domain: string, jwt: string | null) {
        this._panel = panel;
        this._panel.onDidDispose(() => this.dispose(), null);
        this._panel.webview.html = this._getHtmlForWebview(roomName, domain, jwt);
    }

    public static createOrShow(extensionUri: vscode.Uri, roomName: string, domain: string, jwt: string | null = null) {
        const column = vscode.window.activeTextEditor
            ? vscode.window.activeTextEditor.viewColumn
            : undefined;

        if (VideoPanel.currentPanel) {
            VideoPanel.currentPanel._panel.reveal(column);
            // We could also re-initialize the iframe if room changed, but assuming one room at a time.
            return;
        }

        const panel = vscode.window.createWebviewPanel(
            'visionlabVideo',
            'VisionLab Video Call',
            column || vscode.ViewColumn.Beside,
            { 
                enableScripts: true,
                retainContextWhenHidden: true
            }
        );

        VideoPanel.currentPanel = new VideoPanel(panel, extensionUri, roomName, domain, jwt);
    }

    private _getHtmlForWebview(roomName: string, domain: string, jwt: string | null) {
        const jwtParam = jwt ? `?jwt=${jwt}` : '';
        const url = `https://${domain}/${roomName}${jwtParam}#config.prejoinPageEnabled=false&config.disableDeepLinking=true&config.brandingDataUrl=''&interfaceConfig.SHOW_JITSI_WATERMARK=false&interfaceConfig.SHOW_BRAND_WATERMARK=false&interfaceConfig.SHOW_PROMOTIONAL_CLOSE_PAGE=false&interfaceConfig.TOOLBAR_BUTTONS=["microphone","camera","closedcaptions","desktop","fullscreen","fodeviceselection","hangup","profile","chat","settings","videoquality","filmstrip","feedback","shortcuts","tileview","select-background","download","help","mute-everyone","security"]`;

        return `<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>VisionLab Video Call</title>
                <style>
                    body, html { margin: 0; padding: 0; height: 100vh; overflow: hidden; background-color: #050505; }
                    iframe { width: 100%; height: 100%; border: none; }
                </style>
            </head>
            <body>
                <iframe src="${url}" allow="camera; microphone; display-capture" allowfullscreen="true"></iframe>
            </body>
            </html>`;
    }

    public dispose() {
        VideoPanel.currentPanel = undefined;
        this._panel.dispose();
    }
}
