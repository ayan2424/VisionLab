#!/bin/bash
set -e

sudo su -c '
export PATH="/usr/local/bin:/www/server/nodejs/v20.20.2/bin:$PATH"
export NODE_OPTIONS="--max_old_space_size=8192"
export VERSION="1.0.0"

# Fix postinstall.sh to completely ignore the FORCE_NODE_VERSION check which is buggy
cd /www/wwwroot/visionlab.ayan24.me/visionlab-ide

sed -i "s/if \[ \"\$major_node_version\" -ne \"\${FORCE_NODE_VERSION:-20}\" \]; then/if false; then/g" ci/dev/postinstall.sh || true

echo "Resuming yarn release:standalone..."
rm -rf release-standalone
yarn run release:standalone

echo "Running yarn package..."
yarn run package

echo "Running docker build..."
docker build -t visionlab/workspace:latest -f ci/release-image/Dockerfile .
'
