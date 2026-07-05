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

echo "Running yarn install..."
yarn install --frozen-lockfile

echo "Running yarn build..."
yarn build

echo "Running yarn build:vscode..."
yarn build:vscode

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
