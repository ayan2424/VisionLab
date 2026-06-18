<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckLateSubs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'submissions:check-late';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark in_progress submissions as late if the assignment due date has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = \App\Models\Submission::where('status', 'in_progress')
            ->whereHas('assignment', function ($query) {
                $query->whereNotNull('due_date')
                      ->where('due_date', '<', now());
            })
            ->update(['status' => 'late']);

        $this->info("Marked {$count} submissions as late.");
    }
}
