#!/bin/bash
set -e

sudo su -c '
echo "Killing existing node/yarn processes..."
killall node || true
killall yarn || true

export PATH="/usr/local/bin:/www/server/nodejs/v20.20.2/bin:$PATH"
export NODE_OPTIONS="--max_old_space_size=8192"
export VERSION="1.0.0"

cd /www/wwwroot/visionlab.ayan24.me/visionlab-ide

echo "Patching ci/build/npm-postinstall.sh perfectly..."
python3 -c "
import sys
content = open("ci/build/npm-postinstall.sh", "r").read()
if "VSCODE_INSTALL_RUNNING" not in content:
    content = content.replace("main() {", "main() {\n  if [ -n \"\${VSCODE_INSTALL_RUNNING:-}\" ]; then echo \"Preventing infinite loop.\"; exit 0; fi\n  export VSCODE_INSTALL_RUNNING=1")
    open("ci/build/npm-postinstall.sh", "w").write(content)
"

# Also remove the broken one we injected
sed -i "/if \[ -n \"\" \]; then echo \"Preventing infinite loop.\"; exit 0; fi/d" ci/build/npm-postinstall.sh || true
sed -i "/export VSCODE_INSTALL_RUNNING=1/d" ci/build/npm-postinstall.sh || true

python3 -c "
import sys
content = open("ci/build/npm-postinstall.sh", "r").read()
if "VSCODE_INSTALL_RUNNING" not in content:
    content = content.replace("main() {", "main() {\n  if [ -n \"\${VSCODE_INSTALL_RUNNING:-}\" ]; then echo \"Preventing infinite loop.\"; exit 0; fi\n  export VSCODE_INSTALL_RUNNING=1")
    open("ci/build/npm-postinstall.sh", "w").write(content)
"

echo "Re-running yarn release to properly populate release folder..."
yarn run release

echo "Running yarn release:standalone..."
rm -rf release-standalone
yarn run release:standalone

echo "Running yarn package..."
yarn run package

echo "Running docker build..."
docker build -t visionlab/workspace:latest -f ci/release-image/Dockerfile .
'
