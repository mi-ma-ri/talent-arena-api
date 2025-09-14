<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
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
            //
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $messsages = [];
        foreach ($validator->errors()->messages() as $message) {
            $messsages[] = implode("\n", $message);
        }
        $response = response()->json([
            'result_code' => 400,
            'result_message' => implode("\n", $messsages),
        ], 422);
        throw new HttpResponseException($response);
    }

    public function validationData(): array
    {
        // GETの場合も含めてクエリパラメータを使う
        return $this->query();
    }
}
