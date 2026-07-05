#!/bin/bash
set -e

sudo su -c '
set -e
cd /www/wwwroot/visionlab.ayan24.me/visionlab-ide

echo "Patching entrypoint.sh to disable auth..."
sed -i "s/exec dumb-init \/usr\/bin\/code-server \"\$@\"/exec dumb-init \/usr\/bin\/code-server --auth none \"\$@\"/g" ci/release-image/entrypoint.sh

echo "Rebuilding Docker image..."
docker build -t visionlab/workspace:latest -f ci/release-image/Dockerfile .

echo "Recreating test container..."
docker rm -f visionlab_test_container || true
docker run -d --name visionlab_test_container -p 8080:8080 visionlab/workspace:latest
'
