<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\LoginGetRestoreRequest;


class LoginController extends Controller
{
    public function __construct(
        private LoginService $login_service,
    ) {}

    /**
     * ログイン処理 メール・パスワードの一致確認
     * @param $request->email
     * @param $request->password
     * @param $request->status
     * @return bool True
     */
    public function LoginGetRestoreRequest(LoginGetRestoreRequest $request)
    {
        try {
            $result = [
                'result_code' => 200,
                'result_message' => 'OK',
            ];
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $result = [
                'result_code' => 400,
                'result_message' => $e->getMessage(),
            ];
        }
        return response()->json($result);
    }
}
