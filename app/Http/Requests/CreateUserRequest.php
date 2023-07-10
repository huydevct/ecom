<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateUserRequest extends FormRequest
{
    public function authorize(){
        return true;
    }

    public function rules(){
        return [
            'first_name' => [
                'required',

            ],
            'address' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
            'password' => 'required'
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator, response()->json([
            'success' => false,
            'message' => $validator->messages()->first(),
        ]));
    }
}
