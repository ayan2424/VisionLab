<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkspaceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization should be handled by policies/gates in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'course_id' => ['required', 'exists:courses,id'],
            'template_id' => ['nullable', 'exists:workspace_templates,id'],
            'ram_limit' => ['nullable', 'integer', 'min:512', 'max:8192'],
            'cpu_limit' => ['nullable', 'numeric', 'min:0.5', 'max:8.0'],
            'type' => ['required', 'in:standard,exam,sandbox'],
        ];
    }
}
