@echo off
echo Running official build commands directly to avoid CRLF issues...
docker run --rm -v "%CD%\continue-source:/workspace" visionlab-extension-builder /bin/bash -c "cd /workspace && npm install && npm install extend ecdsa-sig-formatter @ai-sdk/provider @ai-sdk/deepseek ai --no-save && rm -rf /workspace/packages/openai-adapters/node_modules && node ./scripts/build-packages.js && cd core && export PUPPETEER_SKIP_DOWNLOAD='true' && npm install && npm link && cd ../gui && npm install && npm link @continuedev/core && NODE_OPTIONS='--max-old-space-size=4096' npm run build && cd ../extensions/vscode && npm install && npm link @continuedev/core && npm run package"
echo Build complete!
