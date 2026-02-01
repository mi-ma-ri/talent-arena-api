<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\LoginGetRestoreRequest;
use App\Services\LoginService;


class LoginController extends Controller
{
    public function __construct(
        private LoginService $login_service,
    ) {}

    /**
     * ログイン処理 メール・パスワードの一致確認
     * @param $request->email
     * @param $request->password
     * @return bool
     */
    public function login(LoginGetRestoreRequest $request)
    {
        try {
            $player = $this->login_service->authenticate($request->email, $request->password);
            if (!$player) {
                throw new Exception('メールアドレスまたはパスワードが正しくありません。');
            }
            $result = [
                'result_code' => 200,
                'result_message' => 'OK',
            ];
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $result = [
                'result_code' => 401,
                'result_message' => $e->getMessage(),
            ];
        }
        return response()->json($result);
    }
}
