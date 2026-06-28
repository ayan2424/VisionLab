import os
import json
import shutil

repo_dir = r"storage\extensions\continue-source"
logo_src = r"public\icons\logo-orange.svg"

print("1. Updating package.json...")
pkg_path = os.path.join(repo_dir, "extensions", "vscode", "package.json")
if os.path.exists(pkg_path):
    with open(pkg_path, "r", encoding="utf-8") as f:
        pkg = json.load(f)
    pkg["name"] = "visionlab-agent"
    pkg["displayName"] = "VisionLab Agent"
    pkg["publisher"] = "visionlab"
    pkg["author"] = "Aptech Vision 2026"
    pkg["description"] = "VisionLab Autonomous Collaborative Agent"
    pkg["repository"] = {"type": "git", "url": "https://visionlab.local"}
    
    props = pkg.get("contributes", {}).get("configuration", {}).get("properties", {})
    if "continue.telemetryEnabled" in props:
        del props["continue.telemetryEnabled"]
    
    with open(pkg_path, "w", encoding="utf-8") as f:
        json.dump(pkg, f, indent=4)
else:
    print("package.json not found!")

print("2. Replacing API endpoints and strings...")
# Be careful to not replace variable names or command IDs
targets = {
    "api.continue.dev": "host.docker.internal:8000/api/ai",
    "https://api.continue.dev": "http://host.docker.internal:8000/api/ai",
    "continue.shareSession": "visionlab.disabled",
    "continue.enterEnterpriseLicenseKey": "visionlab.disabled",
    "telemetry.continue.dev": "localhost:9999/null",
    '"Continue"': '"VisionLab Agent"',
    "'Continue'": "'VisionLab Agent'",
    ">Continue<": ">VisionLab Agent<",
    "Continue Console": "VisionLab Agent Console",
}

modified_files = 0
for root, _, files in os.walk(repo_dir):
    if "node_modules" in root or ".git" in root or "build" in root or "out" in root:
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

print(f"Deep surgical replacement complete. Modified {modified_files} files.")

print("3. Replacing logos...")
# Common logo paths in Continue source
logo_paths = [
    os.path.join(repo_dir, "extensions", "vscode", "media", "continue.svg"),
    os.path.join(repo_dir, "extensions", "vscode", "media", "continue-logo.png"), 
    os.path.join(repo_dir, "gui", "public", "continue_logo.png"),
    os.path.join(repo_dir, "gui", "public", "continue_logo.svg"),
]

for lp in logo_paths:
    if os.path.exists(lp):
        shutil.copy(logo_src, lp)
        print(f"Replaced logo at {lp}")
