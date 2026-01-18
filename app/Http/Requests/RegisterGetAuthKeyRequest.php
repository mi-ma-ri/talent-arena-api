<?php

namespace App\Http\Requests;

use App\Consts\CommonConsts;
use App\Services\RegisterService;
use Closure;

class RegisterGetAuthKeyRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                function (string $attribute, mixed $value, Closure $fail) {
                    $register_service = new RegisterService();
                    $registered = $register_service->findByEmail($value);

                    if ($registered && $registered->user_status == CommonConsts::IS_MEMBER) {
                        $fail('既に使用されている:attributeです。');
                    }
                },
            ],
        ];
    }
}
