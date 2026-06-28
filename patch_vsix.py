import os
import shutil

repo_dir = r"temp_vsix"
logo_src = r"public\icons\logo-orange.svg"

print("Replacing strings in VSIX...")
targets = {
    '"Continue"': '"VisionLab Agent"',
    "'Continue'": "'VisionLab Agent'",
    ">Continue<": ">VisionLab Agent<",
    "Continue Console": "VisionLab Agent Console",
}

modified_files = 0
for root, _, files in os.walk(repo_dir):
    for file in files:
        if file.endswith(('.js', '.json', '.html', '.md')):
            filepath = os.path.join(root, file)
            try:
                with open(filepath, "r", encoding="utf-8") as f:
                    content = f.read()
                
                modified = False
                for search, replace in targets.items():
                    if search in content:
                        content = content.replace(search, replace)
                        modified = True
                
                # Update publisher natively in package.json
                if file == "package.json":
                    content = content.replace('"publisher": "Continue"', '"publisher": "visionlab"')
                    content = content.replace('"name": "continue"', '"name": "visionlab-agent"')
                    modified = True

                if modified:
                    with open(filepath, "w", encoding="utf-8") as f:
                        f.write(content)
                    modified_files += 1
            except Exception:
                pass

print(f"VSIX patch complete. Modified {modified_files} files.")

print("Replacing logos in VSIX...")
for root, _, files in os.walk(repo_dir):
    for file in files:
        if "continue" in file.lower() and (file.endswith('.svg') or file.endswith('.png')):
            lp = os.path.join(root, file)
            shutil.copy(logo_src, lp)
            print(f"Replaced logo at {lp}")

