#!/bin/bash
set -e

sudo su -c '
set -e
export PATH="/usr/local/bin:/www/server/nodejs/v20.20.2/bin:$PATH"
export NODE_OPTIONS="--max_old_space_size=20480"
export VERSION="1.0.0"
cd /www/wwwroot/visionlab.ayan24.me/visionlab-ide

echo "Packaging minified VS Code (skipping recompilation)..."
cd lib/vscode
taskset -c 0-5 yarn gulp vscode-reh-web-linux-x64-min-ci

echo "Fixing script permissions..."
chmod +x ../../ci/build/build-release.sh
chmod +x ../../ci/build/build-standalone-release.sh

cd ../..

echo "Re-running yarn release to properly populate release folder..."
taskset -c 0-5 yarn run release

echo "Running yarn release:standalone..."
rm -rf release-standalone
taskset -c 0-5 yarn run release:standalone

echo "Running yarn package..."
taskset -c 0-5 yarn run package

echo "Running docker build..."
docker build -t visionlab/workspace:latest -f ci/release-image/Dockerfile .
'
