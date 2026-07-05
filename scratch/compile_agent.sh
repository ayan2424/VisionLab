#!/bin/bash
set -e
echo "Compiling VisionLab Agent natively via Docker..."
docker run --rm \
  -v "$(pwd)/storage/extensions/continue-source:/workspace/continue-source" \
  -w /workspace/continue-source \
  node:20-bookworm \
  bash -c "
    apt-get update && apt-get install -y python3 build-essential jq libsecret-1-dev unzip
    npm install
    node ./scripts/build-packages.js
    
    pushd core
    export PUPPETEER_SKIP_DOWNLOAD='true'
    npm install
    npm link
    popd
    
    pushd gui
    npm install
    npm link @continuedev/core
    NODE_OPTIONS=\"--max-old-space-size=4096\" npm run build
    popd
    
    pushd extensions/vscode
    npm install
    npm link @continuedev/core
    npm run package
    popd
  "
echo "VisionLab Agent Compilation complete!"
