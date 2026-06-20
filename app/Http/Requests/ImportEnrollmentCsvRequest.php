<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportEnrollmentCsvRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // For CSV import, the user should be an Instructor or Admin.
        // Course specific authorization is handled via Policy in Controller.
        return $this->user()->isInstructor() || $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ];
    }
    
    public function messages(): array
    {
        return [
            'csv_file.required' => 'Please upload a CSV file to import enrollments.',
            'csv_file.mimes'    => 'The uploaded file must be a valid CSV format.',
            'csv_file.max'      => 'The CSV file size must not exceed 5MB.',
        ];
    }
}
