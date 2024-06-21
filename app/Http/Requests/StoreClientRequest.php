<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:190'],
            'email' => ['nullable', 'required_without:phone', 'email:dns'],
            'phone' => ['nullable', 'required_without:email', 'regex:/^[\d\s\+]+$/'],
        ];
    }
}
