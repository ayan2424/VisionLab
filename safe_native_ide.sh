#!/bin/bash
set -e

sudo su -c '
export PATH="/tmp/node-v20.14.0-linux-x64/bin:$PATH"
# REDUCED MEMORY AND JOBS TO PREVENT SERVER FREEZE
export NODE_OPTIONS="--max_old_space_size=4096"
export JOBS=2
export MAKEFLAGS="-j2"
export PARALLEL_WORKERS=2

cd /www/wwwroot/visionlab.ayan24.me/visionlab-ide

echo "Starting SAFE full build sequence..."
yarn config set ignore-engines false
# install should already be mostly done, but run to be safe
yarn install
# build typescript and native modules safely
yarn build
yarn build:vscode
yarn release
yarn release:standalone
yarn package
docker build -t visionlab/workspace:latest -f ci/release-image/Dockerfile .
'
