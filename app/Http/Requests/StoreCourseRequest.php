<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isInstructor() || $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'Course title is required.',
            'title.max'         => 'Course title must not exceed 120 characters.',
            'cover_image.image' => 'Cover image must be a valid image file.',
            'cover_image.max'   => 'Cover image must be under 2MB.',
        ];
    }
}
