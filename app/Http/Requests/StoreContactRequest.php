<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'institution' => ['required', 'string', 'min:2', 'max:160'],
            'role' => ['nullable', 'string', 'in:faculty,it_admin,dean,student_lead,other'],
            'cohort_size' => ['nullable', 'string', 'in:under_100,100_500,500_2000,over_2000'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'email.required' => 'An institutional email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'institution.required' => 'Please specify your university, college, or department.',
            'message.required' => 'Please share a brief overview of your lab or pilot requirements.',
            'message.min' => 'Please provide at least 10 characters so our engineering team can prepare.',
        ];
    }
}
