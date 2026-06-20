<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JoinCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isStudent();
    }

    public function rules(): array
    {
        return [
            'enrollment_code' => 'required|string|alpha_num|size:8',
        ];
    }

    public function messages(): array
    {
        return [
            'enrollment_code.required'  => 'Please enter an enrollment code.',
            'enrollment_code.alpha_num' => 'Enrollment code must contain only letters and numbers.',
            'enrollment_code.size'      => 'Enrollment code must be exactly 8 characters.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'enrollment_code' => strtoupper(trim($this->enrollment_code ?? '')),
        ]);
    }
}
