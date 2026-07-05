#!/bin/bash
set -e

sudo su -c '
export PATH="/usr/local/bin:/www/server/nodejs/v20.20.2/bin:$PATH"
export NODE_OPTIONS="--max_old_space_size=20480"
export VERSION="1.0.0"
cd /www/wwwroot/visionlab.ayan24.me/visionlab-ide

echo "Patching ci/build/build-vscode.sh..."
sed -i "s/git checkout product.json/# git checkout product.json/g" ci/build/build-vscode.sh

echo "Killing previous build..."
killall node || true
killall yarn || true
sleep 2

echo "Running yarn install..."
taskset -c 0-5 yarn install --frozen-lockfile

echo "Running yarn build..."
taskset -c 0-5 yarn build

echo "Running yarn build:vscode..."
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
