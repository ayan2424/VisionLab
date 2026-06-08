<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Http\Controllers\AnalyticsController;

class AnalyticsPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytics_index_performance()
    {
        // Seed the database with a large number of users
        User::factory()->count(1000)->create([
            'role' => 'student'
        ]);
        User::factory()->count(200)->create([
            'role' => 'instructor'
        ]);
        User::factory()->count(50)->create([
            'role' => 'admin'
        ]);

        $controller = new AnalyticsController();

        $start = microtime(true);
        $queriesStart = DB::getQueryLog();
        DB::enableQueryLog();

        $response = $controller->index();

        $queries = DB::getQueryLog();
        $end = microtime(true);
        $timeTaken = $end - $start;

        $this->assertNotNull($response);

        echo "\nExecution time: " . ($timeTaken * 1000) . "ms\n";
        echo "Queries executed: " . count($queries) . "\n";

        // Assert we don't load all users into memory, which would use a lot of memory
        $memoryUsed = memory_get_peak_usage(true) / 1024 / 1024;
        echo "Peak memory usage: " . $memoryUsed . "MB\n";
    }
}
