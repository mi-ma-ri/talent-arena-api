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
     * トークン生成
     * @param $request->email
     * @param $request->password
     * @return array
     */
    public function auth(LoginGetRestoreRequest $request)
    {
        try {
            $player = $this->login_service->auth($request->email, $request->password);
            if ($player == null) {
                throw new Exception('存在しないユーザーです。');
            }
            $token = $player->createToken('player-token')->plainTextToken;

            $result = [
                'result_code' => 200,
                'result_message' => 'OK',
                'token' => $token
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

    /**
     * チームログイン処理 メール・パスワードの一致確認
     * トークン生成
     * @param $request->email
     * @param $request->password
     * @return array
     */
    public function teamAuth(LoginGetRestoreRequest $request)
    {
        try {
            $team = $this->login_service->teamAuth($request->email, $request->password);
            if ($team == null) {
                throw new Exception('存在しないユーザーです。');
            }
            $token = $team->createToken('team-token')->plainTextToken;

            $result = [
                'result_code' => 200,
                'result_message' => 'OK',
                'token' => $token
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
