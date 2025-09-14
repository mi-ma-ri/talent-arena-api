<?php

namespace App\Http\Requests;

use App\Services\RegisterService;
use Illuminate\Foundation\Http\FormRequest;
use Closure;

class RegisterExistsKeyRequest extends FormRequest
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
        $service = new RegisterService;

        return [
            'table' => [
                'bail',
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                if (!$service->containsKey($value)) {
                        $fail('指定したテーブルは存在しません。');
                    }
                },
            ],
            'key' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'key.required' => '正しい認証キーではありません。',
        ];
    }
}
