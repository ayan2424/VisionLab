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
            [
                'name' => 'Python 3.11 (Data Science)',
                'description' => 'A robust Python environment pre-configured with Pip and data science readiness.',
                'language' => 'python',
                'start_command' => 'python solution.py',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    pkgs.python311
    pkgs.python311Packages.pip
    pkgs.python311Packages.virtualenv
  ];

  shellHook = ''
    echo "Welcome to the VisionLab Python Workspace"
    python --version
  '';
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
# Setup virtual environment automatically if it doesn't exist
if [ ! -d ".venv" ]; then
    python -m venv .venv
fi
# Activate venv
source .venv/bin/activate
# Install basic DS packages
pip install --upgrade pip
echo "Bootstrap complete."
BASH,
                'ui_parameters' => json_encode(['icon' => 'python', 'theme' => 'dark']),
            ],
            [
                'name' => 'Node.js 20 (React/Express)',
                'description' => 'Modern JavaScript environment with Node 20, npm, and yarn pre-installed.',
                'language' => 'javascript',
                'start_command' => 'npm start',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    pkgs.nodejs_20
    pkgs.yarn
  ];

  shellHook = ''
    echo "Welcome to the VisionLab Node.js Workspace"
    node --version
    npm --version
  '';
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
if [ -f "package.json" ]; then
    echo "Running npm install..."
    npm install
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'javascript', 'theme' => 'dark']),
            ],
            [
                'name' => 'PHP 8.3 (Laravel / CLI)',
                'description' => 'PHP 8.3 environment with Composer and SQLite, ready for Laravel or pure PHP scripting.',
                'language' => 'php',
                'start_command' => 'php solution.php',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    pkgs.php83
    pkgs.php83Packages.composer
    pkgs.sqlite
  ];

  shellHook = ''
    echo "Welcome to the VisionLab PHP Workspace"
    php -v
  '';
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
if [ -f "composer.json" ]; then
    composer install
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'php', 'theme' => 'dark']),
            ],
            [
                'name' => 'C / C++ (GCC)',
                'description' => 'Systems programming environment equipped with GCC, Make, and GDB.',
                'language' => 'cpp',
                'start_command' => 'make run',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    pkgs.gcc
    pkgs.gnumake
    pkgs.gdb
    pkgs.cmake
  ];

  shellHook = ''
    echo "Welcome to the VisionLab C/C++ Workspace"
    gcc --version
  '';
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
# Generate basic Makefile if it doesn't exist
if [ ! -f "Makefile" ] && [ -f "solution.cpp" ]; then
cat <<EOF > Makefile
all: solution

solution: solution.cpp
	g++ -std=c++17 -o solution solution.cpp

run: solution
	./solution
EOF
fi
BASH,
                'ui_parameters' => json_encode(['icon' => 'cpp', 'theme' => 'dark']),
            ],
            [
                'name' => 'Java 17 (Spring/CLI)',
                'description' => 'Java environment running OpenJDK 17 with Maven for dependency management.',
                'language' => 'java',
                'start_command' => 'java Solution.java',
                'is_active' => true,
                'nix_config' => <<<NIX
{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    pkgs.jdk17
    pkgs.maven
  ];

  shellHook = ''
    echo "Welcome to the VisionLab Java Workspace"
    java -version
  '';
}
NIX,
                'bootstrap_script' => <<<BASH
#!/bin/sh
echo "Java Workspace Bootstrapped"
BASH,
                'ui_parameters' => json_encode(['icon' => 'java', 'theme' => 'dark']),
            ],
            [
                'name' => 'Full Stack (Admin/Teacher Default)',
                'description' => 'A comprehensive environment with PHP, Node.js, Python, Java, Go, and Ruby pre-installed. Ideal for general development and administrative tasks.',
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

  shellHook = ''
    export PATH=\$PWD/node_modules/.bin:\$PATH
    echo "=========================================="
    echo "Full Stack Environment Initialized"
    echo "PHP, Node.js, Python, Java, Go, Ruby ready"
    echo "=========================================="
  '';
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
