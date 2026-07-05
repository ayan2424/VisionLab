#!/bin/bash
set -e

sudo su -c '
set -e
export PATH="/usr/local/bin:/www/server/nodejs/v20.20.2/bin:$PATH"
export NODE_OPTIONS="--max_old_space_size=20480"
export VERSION="1.0.0"
cd /www/wwwroot/visionlab.ayan24.me/visionlab-ide

echo "Resuming yarn build:vscode..."
taskset -c 0-5 yarn build:vscode

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
