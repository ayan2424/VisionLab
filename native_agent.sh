#!/bin/bash
set -e
export NODE_OPTIONS="--max_old_space_size=8192"
export JOBS=8
export MAKEFLAGS="-j8"

cd /www/wwwroot/visionlab.ayan24.me/storage/extensions/continue-source
npm install
node ./scripts/build-packages.js || true
cd extensions/vscode
npm install
npm run package
