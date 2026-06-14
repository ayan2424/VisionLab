<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $slug   = $this->route('slug');
        $course = \App\Models\Course::where('slug', $slug)->first();
        if (!$course) {
            return false;
        }
        return $this->user()->isAdmin() || $this->user()->id === $course->instructor_id;
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'   => 'sometimes|boolean',
        ];
    }
}
