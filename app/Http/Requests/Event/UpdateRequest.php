<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required'],
            'category_id' => ['required', 'exists:categories,id'],
            'type' => ['required'],
            'description' => ['required'],
            'location' => ['required'],
            'venue' => ['required_if:location,venue'],
            'online' => ['required_if:location,online'],
            'documents' => ['nullable']
        ];
    }
}
