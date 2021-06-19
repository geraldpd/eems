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
            'category_id' => ['required'],
            'name' => ['required'],
            'type' => ['required'],
            'description' => ['required'],
            'location' => ['required'],
            'documents' => ['nulllable'],
            //'schedule_start' => ['required'], //?range and single day
            //'schedule_end' => ['required'], //?range and single day
            'date' => ['required'], //! Not in the fillable and table, but is added in the form
            //'status' => ['required'],
        ];
    }
}
