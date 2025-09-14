<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use stdClass;
use App\Consts\CommonConsts;
use RegisterExistsKeyRequest;
use App\Http\Requests\RegisterGetAuthKeyRequest;
use App\Services\RegisterService;

class RegisterController extends Controller
{
    public function __construct(
        private RegisterService $register_service,
    ) {}

    /**
     * 仮登録用の認証キーとrole_idを返す
     * @param $request->email
     * @param $request->user_status
     * @param $request->user_type
     * @return string $key
     * @return int $role_id
     */
    public function getRegisterAuthKey(RegisterGetAuthKeyRequest $request)
    {
        try {
            $key = $this->register_service->getRegisterAuthKey(
                $request->email,
                $request->user_status,
                $request->user_type,
            );
            $result = [
                'result_code' => 200,
                'result_message' => 'OK',
                'key' => $key->key,
                'role_id' => $key->role_id
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
            if($this->register_service->existsKey($request->table, $request->key)) {
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
}
