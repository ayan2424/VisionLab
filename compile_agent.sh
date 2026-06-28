#!/bin/bash
set -e
echo "Compiling VisionLab Agent natively via Docker..."
docker run --rm \
  -v "$(pwd)/storage/extensions/continue-source:/workspace/continue-source" \
  -w /workspace/continue-source/extensions/vscode \
  node:20-bookworm \
  bash -c "
    apt-get update && apt-get install -y python3 build-essential jq libsecret-1-dev unzip
    npm install
    npm run package
  "
echo "VisionLab Agent Compilation complete!"
