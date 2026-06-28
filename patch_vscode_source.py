import os

repo_dir = r"visionlab-ide\lib\vscode\src\vs"

targets = {
    '"VS Code"': '"VisionLab IDE"',
    "'VS Code'": "'VisionLab IDE'",
    "VS Code's": "VisionLab IDE's",
    "with VS Code": "with VisionLab IDE",
    "into VS Code": "into VisionLab IDE",
    "in VS Code": "in VisionLab IDE"
}

modified_files = 0
for root, _, files in os.walk(repo_dir):
    if "node_modules" in root or ".git" in root:
        continue
    for file in files:
        if file.endswith(('.ts', '.tsx', '.js', '.json', '.html', '.md')):
            filepath = os.path.join(root, file)
            try:
                with open(filepath, "r", encoding="utf-8") as f:
                    content = f.read()
                
                modified = False
                for search, replace in targets.items():
                    if search in content:
                        content = content.replace(search, replace)
                        modified = True
                
                if modified:
                    with open(filepath, "w", encoding="utf-8") as f:
                        f.write(content)
                    modified_files += 1
            except Exception:
                pass

print(f"Deep surgical replacement for VS Code complete. Modified {modified_files} files.")
