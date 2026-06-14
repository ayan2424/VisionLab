<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $course = $this->route('course');
        return $this->user()->isAdmin() || $this->user()->id === $course->instructor_id;
    }

    public function rules(): array
    {
        return [
            'title'            => 'required|string|max:150',
            'description'      => 'nullable|string|max:5000',
            'max_points'       => 'required|integer|min:1|max:1000',
            'due_date'         => 'nullable|date|after:now',
            'starter_code'     => 'nullable|string|max:20000',
            'starter_language' => 'required|string|in:python,javascript,typescript,php,java,c,cpp,rust,go,ruby,bash',
            'auto_workspace'   => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'Assignment title is required.',
            'max_points.required'     => 'Maximum points must be specified.',
            'max_points.min'          => 'Maximum points must be at least 1.',
            'due_date.after'          => 'Due date must be in the future.',
            'starter_language.in'     => 'Selected language is not supported.',
        ];
    }
}
