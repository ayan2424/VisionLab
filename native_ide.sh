#!/bin/bash
set -e

sudo su -c '
export PATH="/tmp/node-v20.14.0-linux-x64/bin:$PATH"
export NODE_OPTIONS="--max_old_space_size=8192"
export JOBS=8
export MAKEFLAGS="-j8"
export PARALLEL_WORKERS=8

cd /www/wwwroot/visionlab.ayan24.me/visionlab-ide

echo "Fixing ALL CRLF just in case..."
find . -type f -print0 | xargs -0 dos2unix 2>/dev/null || find . -type f -exec sed -i "s/\r$//" {} +
chmod -R +x ./ci

yarn config set ignore-engines false
yarn install
yarn build
yarn build:vscode
yarn release
yarn release:standalone
yarn package
docker build -t visionlab/workspace:latest -f ci/release-image/Dockerfile .
'
