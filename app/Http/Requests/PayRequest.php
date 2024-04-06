<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'AccountId' => 'required'
        ];
    }
}
