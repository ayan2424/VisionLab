#!/bin/bash
set -e
echo "Compiling VisionLab IDE natively via Docker..."
docker run --rm \
  -v "$(pwd)/visionlab-ide:/workspace/visionlab-ide" \
  -w /workspace/visionlab-ide \
  node:20-bookworm \
  bash -c "
    apt-get update && apt-get install -y python3 build-essential jq
    yarn install
    yarn release
  "
echo "Compilation complete!"
