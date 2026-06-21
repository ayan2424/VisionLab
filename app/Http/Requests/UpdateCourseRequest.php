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
            'title'       => 'required|string|max:255',
            'description' => 'required|string|min:10|max:5000',
            'duration'    => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'   => 'sometimes|boolean',
            'allow_marketplace' => 'sometimes|boolean',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'schedule_time' => 'nullable|string|max:255',
            'notes'       => 'nullable|string|max:5000',
        ];
    }
}
