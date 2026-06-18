<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CsvEnrollmentJob implements ShouldQueue
{
    use Queueable, \Illuminate\Foundation\Bus\Dispatchable, \Illuminate\Queue\InteractsWithQueue, \Illuminate\Queue\SerializesModels;

    public string $filePath;
    public int $courseId;
    public int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, int $courseId, int $userId)
    {
        $this->filePath = $filePath;
        $this->courseId = $courseId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!\Illuminate\Support\Facades\Storage::exists($this->filePath)) {
            \Illuminate\Support\Facades\Log::error("CsvEnrollmentJob: CSV file not found at {$this->filePath}");
            return;
        }

        $stream = \Illuminate\Support\Facades\Storage::readStream($this->filePath);
        $header = fgetcsv($stream); // assume first row is header with 'email'

        $emailIndex = array_search('email', array_map('strtolower', $header));
        if ($emailIndex === false) {
            $emailIndex = 0; // fallback to first column
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        while (($row = fgetcsv($stream)) !== false) {
            $email = $row[$emailIndex] ?? null;
            if (!$email) continue;

            $user = \App\Models\User::where('email', $email)->first();
            if ($user) {
                \App\Models\CourseEnrollment::firstOrCreate([
                    'course_id' => $this->courseId,
                    'student_id' => $user->id,
                ], [
                    'status' => 'active'
                ]);
                $success++;
            } else {
                $failed++;
                $errors[] = $email;
            }
        }

        fclose($stream);
        \Illuminate\Support\Facades\Storage::delete($this->filePath);

        $admin = \App\Models\User::find($this->userId);
        if ($admin) {
            // Ideally notify admin. Logging for now as per minimal requirement.
            \Illuminate\Support\Facades\Log::info("CsvEnrollmentJob complete for Course {$this->courseId}. Success: {$success}, Failed: {$failed}", $errors);
        }
    }
}
