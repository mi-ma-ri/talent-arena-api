<?php

namespace App\Http\Requests;

class RegisterPlayerCheckRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'auth_key' => [
                'bail',
                'required',
                'exists:auth_keys,auth_key'
            ]
        ];
    }
}
