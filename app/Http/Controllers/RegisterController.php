<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use stdClass;
use App\Services\RegisterService;
use App\Consts\CommonConsts;
use App\Http\Requests\RegisterGetAuthKeyRequest;
use App\Http\Requests\RegisterExistsKeyRequest;
use App\Http\Requests\RegisterPlayerCheckRequest;

class RegisterController extends Controller
{
    public function __construct(
        private RegisterService $register_service,
    ) {}

    /**
     * 仮登録用の認証キーとsubject_idを返す
     * @param $request->email
     * @param $request->status
     * @param $request->subject_type
     * @return string $key
     * @return int $subject_id
     */
    public function getRegisterAuthKey(RegisterGetAuthKeyRequest $request)
    {
        try {
            # 仮登録用の認証キーとidを取得
            $key = $this->register_service->getRegisterAuthKey(
                $request->email,
                $request->status,
                $request->subject_type,
            );
            $result = [
                'result_code' => 200,
                'result_message' => 'OK',
                'key' => $key->key,
                'subject_id' => $key->subject_id
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

    /**
     * 【説明】認証キーが存在するかどうかを返す
     */
    public function existsKey(RegisterExistsKeyRequest $request)
    {
        $result = new stdClass;
        try {
            if ($this->register_service->existsKey($request->table, $request->key)) {
                $result->result_code = 200;
                $result->result_message = 'OK';
            } else {
                throw new Exception('正しいキーではありません。');
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $result->result_code = '400';
            $result->result_message = $e->getMessage();
        }
        return response()->json($result);
    }
    /**
     * 【説明】登録用のプレイヤー情報を取得する
     * @param $request->key
     * @return object $player
     */
    public function getRegisterPlayer(RegisterPlayerCheckRequest $request)
    {
        Log::info($request);
        $result = new stdClass;
        try {
            $result->result_code = 200;
            $result->result_message = 'OK';
            $result->player = $this->register_service->getRegisterPlayer($request->auth_key);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $result->result_code = '400';
            $result->result_message = $e->getMessage();
        }
        return response()->json($result);
    }

    /**
     * 【説明】選手情報本登録
     * @param $request
     * @return bool true
     */
    public function postJoin(Request $request)
    {
        $result = new stdClass;
        try {
            $this->register_service->postJoin(
                $request->all(),
                CommonConsts::IS_MEMBER,
            );
            $result->result_code = 200;
            $result->result_message = 'OK';
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $result->result_code = '400';
            $result->result_message = $e->getMessage();
        }
        return response()->json($result);
    }
}
