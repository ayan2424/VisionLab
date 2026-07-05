#!/usr/bin/env python3
import sys

filepath = "/www/wwwroot/visionlab.ayan24.me/visionlab-ide/ci/build/npm-postinstall.sh"
with open(filepath, "r") as f:
    content = f.read()

# Fix the broken if statement
content = content.replace('if [ -n "" ]; then echo "Preventing infinite loop."; exit 0; fi', 'if [ -n "${VSCODE_INSTALL_RUNNING:-}" ]; then echo "Preventing infinite loop."; exit 0; fi')

with open(filepath, "w") as f:
    f.write(content)

print("Fixed npm-postinstall.sh!")
