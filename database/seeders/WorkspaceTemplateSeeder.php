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
        WorkspaceTemplate::firstOrCreate(
            ['name' => 'Full Stack (Admin/Teacher Default)'],
            [
                'description' => 'A comprehensive environment with PHP, Node.js, Python, Java, Go, and Ruby pre-installed. Ideal for general development and administrative tasks.',
                'language' => 'all',
                'start_command' => 'echo "Welcome to the Full Stack Workspace!"',
                'is_active' => true,
                'nix_config' => '{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    # PHP & Composer
    pkgs.php83
    pkgs.php83Packages.composer
    
    # Node.js & Tooling
    pkgs.nodejs_20
    pkgs.yarn
    pkgs.bun
    
    # Python Environment
    pkgs.python311
    pkgs.python311Packages.pip
    
    # Java
    pkgs.jdk17
    
    # Go
    pkgs.go
    
    # Ruby
    pkgs.ruby
    
    # General Tools
    pkgs.git
    pkgs.curl
    pkgs.wget
    pkgs.unzip
  ];

  shellHook = \'\'
    export PATH=$PWD/node_modules/.bin:$PATH
    echo "=========================================="
    echo "Full Stack Environment Initialized"
    echo "PHP, Node.js, Python, Java, Go, Ruby ready"
    echo "=========================================="
  \'\';
}'
            ]
        );
    }
}

