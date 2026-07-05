#!/bin/bash
set -e

sudo su -c '
# Ensure yarn is installed specifically for our local Node 20
/tmp/node-v20.14.0-linux-x64/bin/npm install -g yarn

export PATH="/tmp/node-v20.14.0-linux-x64/bin:$PATH"
export NODE_OPTIONS="--max_old_space_size=20480"
export JOBS=6
export MAKEFLAGS="-j6"
export PARALLEL_WORKERS=6

cd /www/wwwroot/visionlab.ayan24.me/visionlab-ide

echo "Using Node: $(node -v)"
echo "Using Yarn: $(yarn -v)"

yarn config set ignore-engines true

echo "Running yarn install..."
yarn install --ignore-engines

echo "Running yarn build..."
yarn run build

echo "Running yarn build:vscode..."
yarn run build:vscode

echo "Running yarn release..."
yarn run release

echo "Running yarn release:standalone..."
yarn run release:standalone

echo "Running yarn package..."
yarn run package

echo "Running docker build..."
docker build -t visionlab/workspace:latest -f ci/release-image/Dockerfile .
'
