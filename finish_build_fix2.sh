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

echo "Patching ci/build/npm-postinstall.sh to prevent infinite loops..."
# Insert an infinite loop guard at the top of main()
sed -i "/^main() {/a \\  if [ -n \"\\${VSCODE_INSTALL_RUNNING:-}\" ]; then echo \"Preventing infinite loop.\"; exit 0; fi\\n  export VSCODE_INSTALL_RUNNING=1" ci/build/npm-postinstall.sh

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
