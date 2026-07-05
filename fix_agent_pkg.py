#!/usr/bin/env python3
import json

filepath = "/www/wwwroot/visionlab.ayan24.me/visionlab-ide/lib/vscode/extensions/visionlab-agent/package.json"
with open(filepath, "r") as f:
    data = json.load(f)

if "activationEvents" in data:
    del data["activationEvents"]

with open(filepath, "w") as f:
    json.dump(data, f, indent=2)

print("Removed activationEvents from visionlab-agent package.json!")
