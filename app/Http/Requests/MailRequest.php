<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MailRequest extends FormRequest
{
    /**
    * Determine if the user is authorized to make this request.
    *
    * @return bool
    */
    public function authorize()
    {
        return Auth::user()->hasRole('organizer');
    }

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'subject' => ['required', 'string'],
            'email' => ['required', 'email'],
            'cc' => ['nullable'],
            'bcc' => ['nullable'],
            //'cc.*' => ['email'],
            // 'bcc.*' => ['email'],
            'message' => ['required'],
        ];
    }
}
