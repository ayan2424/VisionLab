<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Workspace;
use Illuminate\Support\Str;

class FixWorkspaceSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workspace:fix-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix empty workspace slugs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $workspaces = Workspace::whereNull('slug')->orWhere('slug', '')->get();
        $this->info('Found ' . $workspaces->count() . ' workspaces missing a slug.');

        foreach ($workspaces as $workspace) {
            $slug = Str::slug($workspace->name);
            $originalSlug = $slug;
            $count = 1;
            while (Workspace::where('slug', $slug)->where('id', '!=', $workspace->id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            $workspace->slug = $slug;
            $workspace->save();
            $this->line("Fixed workspace #{$workspace->id} -> {$workspace->slug}");
        }

        $this->info('All workspace slugs fixed successfully!');
    }
}
