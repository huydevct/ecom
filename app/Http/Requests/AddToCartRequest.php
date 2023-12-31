<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AddToCartRequest extends FormRequest
{
    public function rules(){
        return [
            'product_id' => 'required',
            'amount' => 'required',
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
