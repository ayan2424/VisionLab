import re

with open('resources/views/workspace.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update CSS
css_new = '''#vscode-frame{width:100%;height:100%;border:none;display:block;}
.workspace-container { display:flex; height:calc(100vh - 44px); width:100%; overflow:hidden; }
.file-explorer { width:250px; flex-shrink:0; background:#0d1117; border-right:1px solid #21262d; display:flex; flex-direction:column; }
.resizer { width:4px; background:#21262d; cursor:col-resize; flex-shrink:0; transition:background 0.2s; z-index:10; }
.resizer:hover, .resizer.dragging { background:#F05000; }
.center-pane { flex:1; min-width:0; position:relative; display:flex; flex-direction:column; }
#ai-panel { position:relative; right:auto; top:auto; height:100%; max-height:none; border-radius:0; border:none; border-left:1px solid #21262d; box-shadow:none; display:none; flex-shrink:0; }
#ai-panel:not(.hidden-panel) { display:flex; }'''

content = re.sub(r'#vscode-frame\s*\{[^}]*\}', css_new, content)

# 2. Find where the iframe starts
iframe_start = '<iframe id="vscode-frame"'
if iframe_start in content:
    explorer_html = '''<div class="workspace-container">
    <!-- File Explorer -->
    <div id="file-explorer" class="file-explorer">
        <div style="padding:12px 14px; border-bottom:1px solid #21262d; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; display:flex; justify-content:space-between; align-items:center;">
            <span>Explorer</span>
            <button onclick="fetchFiles()" style="background:none;border:none;color:#64748b;cursor:pointer;" title="Refresh">↻</button>
        </div>
        <div id="file-tree" style="flex:1; overflow-y:auto; padding:8px 0; font-family:'JetBrains Mono',monospace; font-size:12px;">
            <div style="padding:10px; text-align:center; color:#64748b;">Loading files...</div>
        </div>
    </div>
    <div class="resizer" id="resizer-left"></div>
    <!-- Center Pane -->
    <div class="center-pane">
        ''' + iframe_start
    content = content.replace(iframe_start, explorer_html)

# 3. Find where the iframe ends and close center pane
iframe_end = '</iframe>'
if iframe_end in content:
    after_iframe = '''</iframe>
        <div id="vscode-fallback" style="display:none;position:absolute;inset:0;background:#0a0a0a;align-items:center;justify-content:center;flex-direction:column;gap:16px;z-index:50;">
            <div style="width:56px;height:56px;border-radius:16px;background:#F05000;display:flex;align-items:center;justify-content:center;box-shadow:0 0 24px rgba(240,80,0,.5);">
                <svg style="width:28px;height:28px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            </div>
            <div style="text-align:center;">
                <div style="font-size:15px;font-weight:700;color:#f1f5f9;margin-bottom:6px;">Starting VS Code Server…</div>
                <div style="font-size:12px;color:#64748b;max-width:360px;line-height:1.6;">The container is booting. This takes a few seconds on first start.</div>
            </div>
            <button onclick="reloadVsCode()" style="padding:9px 20px;border-radius:10px;background:#F05000;color:#fff;font-size:12px;font-weight:700;border:none;cursor:pointer;box-shadow:0 0 12px rgba(240,80,0,.4);">
                Reload VS Code
            </button>
        </div>
    </div>
    <div class="resizer" id="resizer-right" style="display:none;"></div>'''
    content = content.replace(iframe_end, after_iframe)

# 4. Remove old vscode-fallback completely
content = re.sub(r'\{\{-- Fallback overlay shown if iframe fails --\}\}.*?</div>\s*</div>', '', content, flags=re.DOTALL)

# 5. Find ai-panel and put it inside workspace-container
ai_panel_start = '<div id="ai-panel"'
if ai_panel_start in content:
    content = content.replace(ai_panel_start, '<!-- AI Panel (Right Sidebar) -->\n<div id="ai-panel"')

# 6. Close workspace-container before the VIDEO CALL MODAL
video_call_modal = '{{-- ══════════════════════ VIDEO CALL MODAL ══════════════════════ --}}'
if video_call_modal in content:
    content = content.replace(video_call_modal, '</div><!-- End workspace-container -->\n\n' + video_call_modal)

# 7. Inject resizer JS and fetch files JS at the end of scripts
js_to_add = '''
// ─── Workspace Resizer & File Explorer ───────────────────────────
function initResizers() {
    const leftResizer = document.getElementById('resizer-left');
    const rightResizer = document.getElementById('resizer-right');
    const fileExplorer = document.getElementById('file-explorer');
    const aiPanel = document.getElementById('ai-panel');
    const centerPane = document.querySelector('.center-pane');

    let isResizingLeft = false;
    let isResizingRight = false;

    leftResizer.addEventListener('mousedown', (e) => {
        isResizingLeft = true;
        leftResizer.classList.add('dragging');
        document.body.style.cursor = 'col-resize';
        // Add a temporary overlay to iframe to prevent pointer events stealing
        let overlay = document.createElement('div');
        overlay.id = 'iframe-blocker';
        overlay.style.position = 'absolute';
        overlay.style.inset = '0';
        overlay.style.zIndex = '9999';
        centerPane.appendChild(overlay);
    });

    rightResizer.addEventListener('mousedown', (e) => {
        isResizingRight = true;
        rightResizer.classList.add('dragging');
        document.body.style.cursor = 'col-resize';
        let overlay = document.createElement('div');
        overlay.id = 'iframe-blocker';
        overlay.style.position = 'absolute';
        overlay.style.inset = '0';
        overlay.style.zIndex = '9999';
        centerPane.appendChild(overlay);
    });

    document.addEventListener('mousemove', (e) => {
        if (!isResizingLeft && !isResizingRight) return;
        
        if (isResizingLeft) {
            let newWidth = e.clientX;
            if (newWidth < 150) newWidth = 150;
            if (newWidth > 400) newWidth = 400;
            fileExplorer.style.width = `${newWidth}px`;
        }
        
        if (isResizingRight) {
            let newWidth = window.innerWidth - e.clientX;
            if (newWidth < 250) newWidth = 250;
            if (newWidth > 600) newWidth = 600;
            aiPanel.style.width = `${newWidth}px`;
        }
    });

    document.addEventListener('mouseup', () => {
        if (isResizingLeft) {
            isResizingLeft = false;
            leftResizer.classList.remove('dragging');
        }
        if (isResizingRight) {
            isResizingRight = false;
            rightResizer.classList.remove('dragging');
        }
        document.body.style.cursor = 'default';
        const blocker = document.getElementById('iframe-blocker');
        if (blocker) blocker.remove();
    });
}

async function fetchFiles() {
    const treeDiv = document.getElementById('file-tree');
    try {
        const res = await fetch(`${VC.apiBase}/workspace/${VC.roomSlug}/files`, {
            headers: {'Accept':'application/json'}
        });
        if (!res.ok) throw new Error('Failed to load');
        const data = await res.json();
        
        if (!data.files || data.files.length === 0) {
            treeDiv.innerHTML = '<div style="padding:10px; color:#64748b; text-align:center;">No files found</div>';
            return;
        }
        
        treeDiv.innerHTML = renderTree(data.files);
    } catch(e) {
        treeDiv.innerHTML = '<div style="padding:10px; color:#f87171; text-align:center;">Failed to load files</div>';
    }
}

function renderTree(files, padding = 12) {
    let html = '';
    for (const f of files) {
        if (f.type === 'directory') {
            html += `<div style="padding: 4px 12px 4px ${padding}px; display:flex; align-items:center; gap:6px; color:#94a3b8; cursor:pointer;" onmouseover="this.style.background='#161b22'" onmouseout="this.style.background='transparent'">
                <span style="color:#F05000">📁</span> ${f.name}
            </div>`;
            if (f.children && f.children.length > 0) {
                html += renderTree(f.children, padding + 16);
            }
        } else {
            let icon = '📄';
            if (f.name.endsWith('.py')) icon = '🐍';
            else if (f.name.endsWith('.js') || f.name.endsWith('.ts')) icon = '📜';
            else if (f.name.endsWith('.php')) icon = '🐘';
            else if (f.name.endsWith('.md')) icon = '📝';
            else if (f.name.endsWith('.json')) icon = '⚙️';
            
            html += `<div style="padding: 4px 12px 4px ${padding}px; display:flex; align-items:center; gap:6px; color:#f1f5f9; cursor:pointer;" onmouseover="this.style.background='#161b22'" onmouseout="this.style.background='transparent'">
                <span>${icon}</span> ${f.name}
            </div>`;
        }
    }
    return html;
}

// Hook into DOMContentLoaded to init
document.addEventListener('DOMContentLoaded', () => {
    initResizers();
    fetchFiles();
    
    // Patch toggleAiPanel to also show/hide right resizer
    const origToggle = toggleAiPanel;
    toggleAiPanel = function() {
        origToggle();
        const resizer = document.getElementById('resizer-right');
        if (panelVisible) {
            resizer.style.display = 'block';
        } else {
            resizer.style.display = 'none';
        }
    };
});
</script>
'''

content = content.replace('</script>', js_to_add)

with open('resources/views/workspace.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
