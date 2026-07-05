#!/bin/bash
set -e

sudo su -c '
# FORCE NODE 20 GLOBALLY BY OVERRIDING SYSTEM NODE
if [ ! -f /usr/local/bin/node24_backup ]; then
    mv /usr/local/bin/node /usr/local/bin/node24_backup || true
    mv /usr/local/bin/npm /usr/local/bin/npm24_backup || true
    mv /usr/local/bin/npx /usr/local/bin/npx24_backup || true
    mv /usr/local/bin/yarn /usr/local/bin/yarn24_backup || true
fi

ln -sf /tmp/node-v20.14.0-linux-x64/bin/node /usr/local/bin/node
ln -sf /tmp/node-v20.14.0-linux-x64/bin/npm /usr/local/bin/npm
ln -sf /tmp/node-v20.14.0-linux-x64/bin/npx /usr/local/bin/npx

npm install -g yarn
ln -sf /tmp/node-v20.14.0-linux-x64/bin/yarn /usr/local/bin/yarn

export PATH="/usr/local/bin:$PATH"
export NODE_OPTIONS="--max_old_space_size=20480"
export JOBS=6
export MAKEFLAGS="-j6"
export PARALLEL_WORKERS=6
export VERSION="1.0.0"
export FORCE_NODE_VERSION=true

cd /www/wwwroot/visionlab.ayan24.me/visionlab-ide

echo "Using Node: $(node -v)"

yarn config set ignore-engines true

yarn install --ignore-engines
yarn build
yarn build:vscode
yarn release
yarn release:standalone
yarn package

docker build -t visionlab/workspace:latest -f ci/release-image/Dockerfile .

# REVERT NODE
rm /usr/local/bin/node /usr/local/bin/npm /usr/local/bin/npx /usr/local/bin/yarn
mv /usr/local/bin/node24_backup /usr/local/bin/node || true
mv /usr/local/bin/npm24_backup /usr/local/bin/npm || true
mv /usr/local/bin/npx24_backup /usr/local/bin/npx || true
mv /usr/local/bin/yarn24_backup /usr/local/bin/yarn || true
'
