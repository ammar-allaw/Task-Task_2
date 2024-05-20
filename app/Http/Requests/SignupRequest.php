<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class SignupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'=>['required','string'],
            'email'=>['required','string','unique:users,email,except,id','email'
            ,function ($attribute, $value, $fail) {
            if (!Str::endsWith($value, '@gmail.com')) {                $fail('The '.$attribute.' must be a valid email address ending with @gmail.com.');
                    }
                },
            ],
            'phone_number'=>['required','regex:/^09[0-9]{8}$/','unique:users,phone_number,except,id'],
            'username'=>['required','string','unique:users,username,except,id'],
            'image'=>['required','image','unique:users,image,except,id'],
            'certificate'=>['required','file','unique:users,certificate,except,id'],
            'password'=>['required','string','min:8'],
            'password_confirmation'=>['required','string','min:8'],
        ];
    }
}
