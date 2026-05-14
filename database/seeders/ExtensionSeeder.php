<?php

namespace Database\Seeders;

use App\Models\Extension;
use Illuminate\Database\Seeder;

class ExtensionSeeder extends Seeder
{
    public function run(): void
    {
        $extensions = [
            ['name' => 'VisionCode Collab',   'package_identifier' => 'visioncode.collab',          'version' => '1.0.0', 'description' => 'Real-time collaboration: cursor sharing, live chat, and presence.',      'is_global' => true,  'is_builtin' => true,  'is_active' => true],
            ['name' => 'VisionLabAgent',  'package_identifier' => 'visioncode.ai-agent',         'version' => '1.0.0', 'description' => 'AI-powered code assistant with CHAT, PLAN, and AGENT modes.',            'is_global' => true,  'is_builtin' => true,  'is_active' => true],
            ['name' => 'Patch Reviewer',       'package_identifier' => 'visioncode.patch-reviewer',   'version' => '1.0.0', 'description' => 'Diff viewer for AI patch proposals with approval workflow.',             'is_global' => true,  'is_builtin' => true,  'is_active' => true],
            ['name' => 'GitLens',              'package_identifier' => 'eamodio.gitlens',             'version' => '15.2.0','description' => 'Git supercharged — history, blame, and branch visualization.',          'is_global' => true,  'is_builtin' => true,  'is_active' => true],
            ['name' => 'Prettier',             'package_identifier' => 'esbenp.prettier-vscode',      'version' => '10.4.0','description' => 'An opinionated code formatter for consistent style.',                  'is_global' => true,  'is_builtin' => true,  'is_active' => true],
            ['name' => 'SonarLint',            'package_identifier' => 'sonarsource.sonarlint-vscode','version' => '4.8.0', 'description' => 'Real-time code quality and security analysis.',                        'is_global' => false, 'is_builtin' => true,  'is_active' => true],
            ['name' => 'Code Runner',          'package_identifier' => 'formulahendry.code-runner',   'version' => '0.12.2','description' => 'Run code snippets in any language directly from the editor.',          'is_global' => true,  'is_builtin' => true,  'is_active' => true],
            ['name' => 'Markdown Preview',     'package_identifier' => 'bierner.markdown-preview-github-styles', 'version' => '2.0.4', 'description' => 'GitHub-styled Markdown preview.',                  'is_global' => false, 'is_builtin' => true,  'is_active' => true],
            ['name' => 'Docker Explorer',      'package_identifier' => 'formulahendry.docker-explorer','version' => '0.1.7','description' => 'Explore and manage Docker containers and images.',                     'is_global' => false, 'is_builtin' => false, 'is_active' => false],
            ['name' => 'Database Client',      'package_identifier' => 'cweijan.vscode-database-client2','version' => '7.5.0','description' => 'Universal database client for SQL databases.',                     'is_global' => false, 'is_builtin' => false, 'is_active' => false],
        ];

        foreach ($extensions as $ext) {
            Extension::updateOrCreate(['package_identifier' => $ext['package_identifier']], $ext);
        }

        $this->command->info('✅ Seeded: ' . count($extensions) . ' extensions');
    }
}
