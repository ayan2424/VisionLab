<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function submit(StoreContactRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Log::info('VisionLab campus pilot inquiry submitted', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'institution' => $validated['institution'],
            'role' => $validated['role'] ?? 'unspecified',
            'cohort_size' => $validated['cohort_size'] ?? 'unspecified',
            'ip' => $request->ip(),
            'submitted_at' => now()->toIso8601String(),
        ]);

        $recipient = (string) config('mail.from.address', '');

        if ($recipient !== '' && filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            try {
                $body = implode("\n", [
                    'New VisionLab Campus Pilot Request',
                    '----------------------------------',
                    'Name: ' . $validated['name'],
                    'Email: ' . $validated['email'],
                    'Institution: ' . $validated['institution'],
                    'Role: ' . ($validated['role'] ?? 'unspecified'),
                    'Cohort Size: ' . ($validated['cohort_size'] ?? 'unspecified'),
                    '',
                    'Message:',
                    $validated['message'],
                ]);

                Mail::raw($body, static function ($message) use ($recipient, $validated): void {
                    $message
                        ->to($recipient)
                        ->replyTo($validated['email'], $validated['name'])
                        ->subject('[VisionLab Pilot] ' . $validated['institution']);
                });
            } catch (Throwable $exception) {
                Log::warning('Campus pilot inquiry mail delivery skipped; inquiry preserved in application log.', [
                    'institution' => $validated['institution'],
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('contact')
            ->with('status', 'Your campus pilot request has been recorded. Our engineering team will respond within one business day.');
    }
}
