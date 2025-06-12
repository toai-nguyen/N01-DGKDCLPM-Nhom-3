<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NovelRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'description' => 'required',
            'tags' => 'required|array|min:1',
            'image' => 'required|image|max:5012',
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'The title is required.',
            'description.required' => 'The description is required.',
            'tags.required' => 'At least one tag is required.',
            'image.required' => 'An image is required.',
            'image.image' => 'The file must be an image.',
            'image.max' => 'The image size must not exceed 5MB.',
        ];
    }
}