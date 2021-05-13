<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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
            //'organizer_id' => ['exists:users,id'],
            'category_id' => ['required'],
            'name' => ['required'],
            'type' => ['required'],
            'description' => ['required'],
            'location' => ['required'],
            'documents' => ['nulllable'],
            //'schedule_from' => ['required'],
            //'schedule_to' => ['required'],
            //'status' => ['required'],
        ];
    }
}
