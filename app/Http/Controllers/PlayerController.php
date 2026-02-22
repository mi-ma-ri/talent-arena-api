<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

use App\Services\PlayerService;



class PlayerController extends Controller
{

    public function __construct(
        private PlayerService $player_service,
    ) {}
    /**
     * 選手情報を返す
     * @param $request->token
     * @return array $profile
     */
    public function getProfile(Request $request)
    {
        try {
            $player = $request->user();
            $profile = $this->player_service->getProfileConvertData($player);
            if (!$profile) {
                throw new Exception('選手情報を取得できませんでした。');
            }
            $result = [
                'result_code' => 200,
                'result_message' => 'OK',
                'profile' => $profile
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
     * 動画URL投稿
     * @param $request
     * @return bool $insert
     */
    public function postHandleUrl(Request $request)
    {
        try {
            $player = $request->user();

            $insert = $this->player_service->postHandleUrl($player, $request);
            if (!$insert) {
                throw new Exception('動画URL投稿に失敗しました。');
            }

            $result = [
                'result_code' => 200,
                'result_message' => 'OK',
                'insert' => $insert
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
     * 選手情報更新
     * @param $request->token
     * @return bool $update
     */
    public function postProfileUpdate(Request $request)
    {
        try {
            $player = $request->user();
            $address = $request->address;
            $position = $request->position;
            $id = $player->id;

            $update = $this->player_service->postProfileUpdateConvertData($address, $position, $id);
            if (!$update) {
                throw new Exception('選手情報を更新できませんでした。');
            }

            $result = [
                'result_code' => 200,
                'result_message' => 'OK',
                'update' => $update
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
     * 選手情報更新
     * @param $request->token
     * @return bool $update
     */
    public function getUrl(Request $request)
    {
        try {
            $player = $request->user();
            $video_data = $this->player_service->getUrl($player);
            if (!$video_data) {
                throw new Exception('動画URL一覧データを取得できませんでした。');
            }

            $result = [
                'result_code' => 200,
                'result_message' => 'OK',
                'video_data' => $video_data
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
