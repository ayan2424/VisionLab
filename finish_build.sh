#!/bin/bash
set -e

sudo su -c '
export PATH="/usr/local/bin:/www/server/nodejs/v20.20.2/bin:$PATH"
export NODE_OPTIONS="--max_old_space_size=8192"
export VERSION="1.0.0"
export FORCE_NODE_VERSION=true

cd /www/wwwroot/visionlab.ayan24.me/visionlab-ide

echo "Using Node: $(node -v)"
echo "Using Yarn: $(yarn -v)"
echo "Using Npm: $(npm -v)"

echo "Resuming yarn release:standalone..."
yarn run release:standalone

echo "Running yarn package..."
yarn run package

echo "Running docker build..."
docker build -t visionlab/workspace:latest -f ci/release-image/Dockerfile .
'
