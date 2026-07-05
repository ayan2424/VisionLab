#!/bin/bash
set -e

sudo su -c '
export PATH="/tmp/node-v20.14.0-linux-x64/bin:$PATH"
export NODE_OPTIONS="--max_old_space_size=20480"
export JOBS=6
export MAKEFLAGS="-j6"
export PARALLEL_WORKERS=6

cd /www/wwwroot/visionlab.ayan24.me/visionlab-ide

echo "Starting HIGH POWER full build sequence..."
yarn config set ignore-engines false
# install should already be mostly done, but run to be safe
yarn install
# build typescript and native modules
yarn build
yarn build:vscode
yarn release
yarn release:standalone
yarn package
docker build -t visionlab/workspace:latest -f ci/release-image/Dockerfile .
'
