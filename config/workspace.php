<?php

return [
    'default_files' => [
        [
            'id' => 'main-py',
            'name' => 'main.py',
            'type' => 'file',
            'language' => 'python',
            'content' => "# VisionLab— Workspace\n# Welcome, {{name}}!\n\ndef greet(name: str) -> str:\n    \"\"\"Return a personalised greeting.\"\"\"\n    return f\"Hello, {name}! Welcome to VisionLab.\"\n\nif __name__ == \"__main__\":\n    message = greet(\"World\")\n    print(message)\n",
        ],
        [
            'id' => 'utils-py',
            'name' => 'utils.py',
            'type' => 'file',
            'language' => 'python',
            'content' => "# Utility helpers\n\ndef add(a: int, b: int) -> int:\n    return a + b\n\ndef subtract(a: int, b: int) -> int:\n    return a - b\n\ndef multiply(a: int, b: int) -> int:\n    return a * b\n",
        ],
        [
            'id' => 'index-js',
            'name' => 'index.js',
            'type' => 'file',
            'language' => 'javascript',
            'content' => "// VisionLab— JavaScript Workspace\n\nconst greet = (name) => {\n    return `Hello, \${name}! Welcome to VisionLab.`;\n};\n\nconsole.log(greet('World'));\n",
        ],
        [
            'id' => 'hello-php',
            'name' => 'hello.php',
            'type' => 'file',
            'language' => 'php',
            'content' => "<?php\n\nfunction greet(string \$name): string {\n    return \"Hello, {\$name}! Welcome to VisionLab.\";\n}\n\necho greet('World') . PHP_EOL;\n",
        ],
        [
            'id' => 'readme-md',
            'name' => 'README.md',
            'type' => 'file',
            'language' => 'markdown',
            'content' => "# My VisionLab Workspace\n\n> Powered by **VisionLab** — Aptech Vision 2026\n\n## Getting Started\n\n1. Select a file from the explorer on the left\n2. Edit in the Monaco editor\n3. Press `Ctrl + Enter` to execute\n4. Use the **AI Sidebar** for instant help\n\n## AI Agent Modes\n\n| Mode  | Capability |\n|-------|------------|\n| CHAT  | Ask questions, get explanations |\n| PLAN  | Step-by-step execution plan |\n| AGENT | Autonomous read/write with diff preview |\n",
        ],
    ],
];
