#!/usr/bin/env python3
import json

filepath = "/www/wwwroot/visionlab.ayan24.me/visionlab-ide/lib/vscode/product.json"
with open(filepath, "r") as f:
    data = json.load(f)

data["nodejsRepository"] = "https://nodejs.org"

with open(filepath, "w") as f:
    json.dump(data, f, indent=2)

print("Added nodejsRepository to product.json!")
