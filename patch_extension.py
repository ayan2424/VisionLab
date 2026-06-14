import json
import os
import re

base_path = r'c:\Users\ayans\OneDrive\Documents\A Projects\Aptech\Vision2026\VisionLab\storage\extensions\visionlab.visionlab-ai-1.0.0\extension'
pkg_path = os.path.join(base_path, 'package.json')
ext_js_path = os.path.join(base_path, 'out', 'extension.js')

print("1. Patching package.json...")
with open(pkg_path, 'r', encoding='utf-8') as f:
    pkg = json.load(f)

# Rebrand
pkg['name'] = 'visionlab-agent'
pkg['displayName'] = 'VisionLab Agent'
pkg['publisher'] = 'VisionLab'
pkg['author'] = 'Aptech Vision 2026'
pkg['description'] = 'VisionLab Autonomous Collaborative Agent'
pkg['repository'] = {'type': 'git', 'url': 'https://visionlab.local'}
pkg['bugs'] = {'url': 'https://visionlab.local', 'email': 'support@visionlab.local'}
pkg['homepage'] = 'https://visionlab.local'

# Remove telemetry from config
props = pkg.get('contributes', {}).get('configuration', {}).get('properties', {})
if 'continue.telemetryEnabled' in props:
    del props['continue.telemetryEnabled']

# Remove external commands
commands = pkg.get('contributes', {}).get('commands', [])
commands = [c for c in commands if c.get('command') not in ['continue.enterEnterpriseLicenseKey', 'continue.shareSession']]
pkg['contributes']['commands'] = commands

with open(pkg_path, 'w', encoding='utf-8') as f:
    json.dump(pkg, f, indent=4)

print("2. Patching extension.js (56MB)... This might take a few seconds.")
with open(ext_js_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace endpoints
content = content.replace('https://api.continue.dev', 'http://host.docker.internal:8000/api/ai')
content = content.replace('api.continue.dev', 'host.docker.internal:8000/api/ai')
content = content.replace('https://proxy-server-l6vsfbxjwq-uc.a.run.app', 'http://host.docker.internal:8000/api/ai')
content = content.replace('https://telemetry.continue.dev', 'http://localhost:9999/null')

# Overwrite some PostHog keys to dummy
content = content.replace('phc_JS6XF', 'phc_DISABLED')

with open(ext_js_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Patching complete! Extension is now isolated and rebranded.")
