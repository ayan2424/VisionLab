import os, json, zipfile, urllib.request

# 1. Fix code-server TS error
filepath = '/home/ubuntu/visionlab-ide/lib/vscode/src/vs/workbench/contrib/welcomeGettingStarted/browser/gettingStarted.contribution.ts'
with open(filepath, 'r') as f:
    content = f.read()
content = content.replace('$W9b', 'any_var_name')
with open(filepath, 'w') as f:
    f.write(content)

os.system('cd /home/ubuntu/visionlab-ide && nohup bash -c "VERSION=4.90.3 yarn build:vscode && yarn release && yarn release:standalone" > build_vscode_retry4.log 2>&1 &')

# 2. Download and repack extension
vsix_url = 'https://github.com/continuedev/continue/releases/download/v1.3.40-vscode/continue-linux-x64-1.3.40.vsix'
os.system('mkdir -p /home/ubuntu/visionlab-agent/extensions/vscode/')
out_vsix = '/home/ubuntu/visionlab-agent/extensions/vscode/visionlab-agent-1.0.0.vsix'
temp_vsix = '/home/ubuntu/temp.vsix'

print("Downloading vsix...")
urllib.request.urlretrieve(vsix_url, temp_vsix)

print("Extracting...")
os.system('rm -rf /home/ubuntu/extract_agent && mkdir -p /home/ubuntu/extract_agent && cd /home/ubuntu/extract_agent && unzip -q ../temp.vsix')

pkg_path = '/home/ubuntu/extract_agent/extension/package.json'
with open(pkg_path, 'r') as f:
    pkg = json.load(f)

pkg['name'] = 'visionlab-agent'
pkg['displayName'] = 'VisionLab Agent'
pkg['publisher'] = 'VisionLab'
pkg['description'] = 'VisionLab Autonomous AI Agent'
pkg['version'] = '1.0.0'

with open(pkg_path, 'w') as f:
    json.dump(pkg, f, indent=2)

print("Repacking...")
os.system('cd /home/ubuntu/extract_agent && zip -qr ' + out_vsix + ' *')

# 3. Register extension
print("Registering...")
os.system('php /home/ubuntu/register_extension.php')
