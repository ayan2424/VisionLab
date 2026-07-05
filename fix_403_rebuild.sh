#!/bin/bash
set -e

sudo su -c '
set -e
cd /www/wwwroot/visionlab.ayan24.me/visionlab-ide

echo "Patching Dockerfile to fix 403 Forbidden..."
sed -i "s/RUN --mount=from=packages,src=\/tmp,dst=\/tmp\/packages dpkg -i \/tmp\/packages\/code-server\*\$(dpkg --print-architecture).deb/RUN --mount=from=packages,src=\/tmp,dst=\/tmp\/packages dpkg -i \/tmp\/packages\/code-server*\$(dpkg --print-architecture).deb \&\& chown -R 1000:1000 \/usr\/lib\/code-server/g" ci/release-image/Dockerfile

echo "Rebuilding Docker image..."
docker build -t visionlab/workspace:latest -f ci/release-image/Dockerfile .

echo "Recreating test container..."
docker rm -f visionlab_test_container || true
docker run -d --name visionlab_test_container -p 8080:8080 visionlab/workspace:latest
'
