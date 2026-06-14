<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $submission = $this->route('submission');
        $course = $submission->assignment->course;
        return $this->user()->isAdmin()
            || ($this->user()->isInstructor() && $this->user()->id === $course->instructor_id);
    }

    public function rules(): array
    {
        $maxPoints = $this->route('submission')->assignment->max_points;

        return [
            'grade'    => "required|integer|min:0|max:{$maxPoints}",
            'feedback' => 'nullable|string|max:3000',
        ];
    }

    public function messages(): array
    {
        return [
            'grade.required' => 'A grade is required.',
            'grade.min'      => 'Grade cannot be negative.',
            'grade.max'      => 'Grade cannot exceed the maximum points for this assignment.',
            'feedback.max'   => 'Feedback must not exceed 3000 characters.',
        ];
    }
}
