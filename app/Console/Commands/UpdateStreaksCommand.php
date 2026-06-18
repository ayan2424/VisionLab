<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateStreaksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:update-streaks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset current streaks for users who have not been active in the last day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = \App\Models\User::whereNotNull('last_activity_at')
            ->where('last_activity_at', '<', now()->startOfDay()->subDay())
            ->where('current_streak', '>', 0)
            ->get();

        $count = 0;
        foreach ($users as $user) {
            $user->update(['current_streak' => 0]);
            $count++;
        }

        $this->info("Reset current streak to 0 for {$count} users.");
    }
}
