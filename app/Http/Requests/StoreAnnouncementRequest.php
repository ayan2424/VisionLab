<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        $course = $this->route('course');
        return $this->user()->isAdmin() || $this->user()->id === $course->instructor_id;
    }

    public function rules(): array
    {
        return [
            'title'  => 'required|string|max:200',
            'body'   => 'required|string|max:5000',
            'pinned' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Announcement title is required.',
            'body.required'  => 'Announcement body is required.',
        ];
    }
}
