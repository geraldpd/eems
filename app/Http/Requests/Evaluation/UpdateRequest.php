<?php

namespace App\Http\Requests\Evaluation;

use App\Models\Evaluation;
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
        return $this->route('evaluation')->organizer_id == $this->user()->id && $this->user()->hasRole('organizer');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required',],
            'description' => ['required'],
            'html_form' => ['required'],
            'questions' => ['required'],
            'update_type' => ['required'],
        ];
    }
}
