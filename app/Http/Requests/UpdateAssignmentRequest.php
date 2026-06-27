<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $assignment = $this->route('assignment');
        $course = $assignment->course;
        return $this->user()->isAdmin() || $this->user()->id === $course->instructor_id;
    }

    public function rules(): array
    {
        return [
            'title'            => 'required|string|max:150',
            'description'      => 'nullable|string|max:5000',
            'max_points'       => 'required|integer|min:1|max:1000',
            'due_date'         => 'nullable|date',
            'starter_code'     => 'nullable|string|max:20000',
            'starter_language' => 'required|string|in:python,javascript,typescript,php,java,c,cpp,rust,go,ruby,bash',
            'template_id'      => 'nullable|exists:workspace_templates,id',
        ];
    }
}
