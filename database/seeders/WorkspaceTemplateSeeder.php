<?php

namespace Database\Seeders;

use App\Models\WorkspaceTemplate;
use Illuminate\Database\Seeder;

class WorkspaceTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // 1. HTML/CSS/JS Basic
            [
                'name' => 'HTML / CSS / JS',
                'description' => 'A basic static web project with an index.html, style.css, and app.js.',
                'language' => 'html',
                'start_command' => 'python3 -m http.server 8080',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  buildInputs = [ pkgs.python311 ];
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
if [ ! -f "index.html" ]; then
cat <<EOF > index.html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML/CSS/JS Starter</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Welcome to VisionLab!</h1>
    <script src="app.js"></script>
</body>
</html>
EOF
echo "body { font-family: sans-serif; text-align: center; margin-top: 50px; }" > style.css
echo "console.log('App loaded!');" > app.js
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'html', 'theme' => 'dark']),
            ],
            // 2. React (Vite)
            [
                'name' => 'React 18 (Vite)',
                'description' => 'A modern React Single Page Application powered by Vite and Node 20.',
                'language' => 'javascript',
                'start_command' => 'npm run dev -- --host 0.0.0.0 --port 8080',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  buildInputs = [ pkgs.nodejs_20 ];
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
if [ ! -f "package.json" ]; then
    echo "Scaffolding React Vite Project..."
    npx -y create-vite@latest temp-app --template react
    mv temp-app/* temp-app/.* . 2>/dev/null || true
    rm -rf temp-app
    npm install
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'react', 'theme' => 'dark']),
            ],
            // 3. Next.js
            [
                'name' => 'Next.js 14',
                'description' => 'A full-stack React framework with App Router, powered by Node 20.',
                'language' => 'javascript',
                'start_command' => 'npm run dev',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  buildInputs = [ pkgs.nodejs_20 ];
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
if [ ! -f "package.json" ]; then
    echo "Scaffolding Next.js Project..."
    npx -y create-next-app@latest temp-app --typescript --eslint --tailwind --no-src-dir --app --import-alias "@/*"
    mv temp-app/* temp-app/.* . 2>/dev/null || true
    rm -rf temp-app
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'javascript', 'theme' => 'dark']),
            ],
            // 4. Node.js Express
            [
                'name' => 'Node.js (Express API)',
                'description' => 'A basic Node.js Express server setup.',
                'language' => 'javascript',
                'start_command' => 'node index.js',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  buildInputs = [ pkgs.nodejs_20 ];
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
if [ ! -f "package.json" ]; then
    npm init -y
    npm install express
cat <<EOF > index.js
const express = require('express');
const app = express();
const port = process.env.PORT || 8080;

app.get('/', (req, res) => {
  res.send('Hello World from Express!');
});

app.listen(port, () => {
  console.log(\`Server running at http://localhost:\${port}\`);
});
EOF
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'javascript', 'theme' => 'dark']),
            ],
            // 5. PHP (Basic)
            [
                'name' => 'PHP 8.3 (Basic)',
                'description' => 'Pure PHP 8.3 environment for scripting and simple web projects.',
                'language' => 'php',
                'start_command' => 'php -S 0.0.0.0:8080',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  buildInputs = [ pkgs.php83 pkgs.php83Packages.composer pkgs.sqlite ];
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
if [ ! -f "index.php" ]; then
echo "<?php\n\necho 'Hello from Pure PHP!';\n" > index.php
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'php', 'theme' => 'dark']),
            ],
            // 6. Laravel 11
            [
                'name' => 'Laravel 11 (Starter)',
                'description' => 'A full Laravel 11 framework project ready for development.',
                'language' => 'php',
                'start_command' => 'php artisan serve --host=0.0.0.0 --port=8080',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  buildInputs = [ pkgs.php83 pkgs.php83Packages.composer pkgs.nodejs_20 pkgs.sqlite ];
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
if [ ! -f "artisan" ]; then
    echo "Scaffolding Laravel Project..."
    composer create-project laravel/laravel temp-app
    mv temp-app/* temp-app/.* . 2>/dev/null || true
    rm -rf temp-app
    chmod -R 775 storage bootstrap/cache
    if [ ! -z "\$VISIONCODE_WORKSPACE_ID" ]; then
        sed -i 's/APP_URL=.*/APP_URL=https:\/\/'\$VISIONCODE_WORKSPACE_ID'-8080.visionlab.ayan24.me/' .env
    fi
    npm install
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'php', 'theme' => 'dark']),
            ],
            // 7. Go
            [
                'name' => 'Go (Golang)',
                'description' => 'A standard Go language environment.',
                'language' => 'go',
                'start_command' => 'go run main.go',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  buildInputs = [ pkgs.go ];
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
if [ ! -f "go.mod" ]; then
    go mod init example/hello
cat <<EOF > main.go
package main

import "fmt"

func main() {
    fmt.Println("Hello, World from Go!")
}
EOF
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'go', 'theme' => 'dark']),
            ],
            // 8. Python
            [
                'name' => 'Python 3.11',
                'description' => 'Python environment with virtualenv and pip.',
                'language' => 'python',
                'start_command' => 'python main.py',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  buildInputs = [ pkgs.python311 pkgs.python311Packages.pip pkgs.python311Packages.virtualenv ];
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
if [ ! -d ".venv" ]; then
    python -m venv .venv
fi
source .venv/bin/activate
pip install --upgrade pip

if [ ! -f "main.py" ]; then
echo "print('Hello World from Python!')" > main.py
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'python', 'theme' => 'dark']),
            ],
            // 9. C/C++
            [
                'name' => 'C / C++ (GCC)',
                'description' => 'C and C++ development environment with GCC and Make.',
                'language' => 'cpp',
                'start_command' => 'make run',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  buildInputs = [ pkgs.gcc pkgs.gnumake pkgs.gdb pkgs.cmake ];
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
if [ ! -f "Makefile" ]; then
cat <<EOF > main.cpp
#include <iostream>

int main() {
    std::cout << "Hello, World from C++!" << std::endl;
    return 0;
}
EOF

cat <<EOF > Makefile
all: main

main: main.cpp
	g++ -std=c++17 -o main main.cpp

run: main
	./main
EOF
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'cpp', 'theme' => 'dark']),
            ],
            // 10. Java
            [
                'name' => 'Java 17 (Maven)',
                'description' => 'Java environment running OpenJDK 17 with Maven.',
                'language' => 'java',
                'start_command' => 'mvn compile exec:java -Dexec.mainClass="com.example.App"',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  buildInputs = [ pkgs.jdk17 pkgs.maven ];
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
if [ ! -f "pom.xml" ]; then
    mvn archetype:generate -DgroupId=com.example -DartifactId=my-app -DarchetypeArtifactId=maven-archetype-quickstart -DinteractiveMode=false
    mv my-app/* my-app/.* . 2>/dev/null || true
    rm -rf my-app
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'java', 'theme' => 'dark']),
            ],
            // 11. C# (.NET)
            [
                'name' => 'C# (.NET 8)',
                'description' => '.NET 8 environment for C# applications.',
                'language' => 'csharp',
                'start_command' => 'dotnet run',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  buildInputs = [ pkgs.dotnet-sdk_8 ];
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
if [ ! -f "Program.cs" ] && [ ! -f "*.csproj" ]; then
    dotnet new console -n MyApp -o temp-app
    mv temp-app/* temp-app/.* . 2>/dev/null || true
    rm -rf temp-app
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'csharp', 'theme' => 'dark']),
            ],
            // 12. Flutter
            [
                'name' => 'Flutter (Web)',
                'description' => 'Flutter SDK for web applications.',
                'language' => 'dart',
                'start_command' => 'flutter run -d web-server --web-port 8080 --web-hostname 0.0.0.0',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  buildInputs = [ pkgs.flutter ];
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
if [ ! -f "pubspec.yaml" ]; then
    flutter create temp_app
    mv temp_app/* temp_app/.* . 2>/dev/null || true
    rm -rf temp_app
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'flutter', 'theme' => 'dark']),
            ],
            // 13. Full Stack
            [
                'name' => 'Full Stack (Admin/Teacher Default)',
                'description' => 'A comprehensive environment with PHP, Node.js, Python, Java, Go, and Ruby pre-installed.',
                'language' => 'all',
                'start_command' => 'echo "Welcome to the Full Stack Workspace!"',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  buildInputs = [
    pkgs.php83 pkgs.php83Packages.composer
    pkgs.nodejs_20 pkgs.yarn pkgs.bun
    pkgs.python311 pkgs.python311Packages.pip
    pkgs.jdk17 pkgs.go pkgs.ruby
    pkgs.git pkgs.curl pkgs.wget pkgs.unzip
  ];
}
NIX,
                'bootstrap_script' => "#!/bin/sh\necho 'Full Stack Ready'",
                'ui_parameters' => json_encode(['icon' => 'globe', 'theme' => 'dark']),
            ]
        ];

        foreach ($templates as $templateData) {
            WorkspaceTemplate::updateOrCreate(
                ['name' => $templateData['name']],
                $templateData
            );
        }
    }
}
