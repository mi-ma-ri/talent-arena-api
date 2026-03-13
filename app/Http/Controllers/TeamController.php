<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

use App\Services\TeamService;


class TeamController extends Controller
{
    public function __construct(
        private TeamService $team_service,
    ) {}

    /**
     * チーム情報を返す
     * @param $request->token
     * @return array $profile
     */
    public function getTeamProfile(Request $request)
    {
        try {
            $team = $request->user();
            $profile = $this->team_service->getProfileConvertData($team);
            if (!$profile) {
                throw new Exception('チーム情報を取得できませんでした。');
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
     * チーム情報更新
     * @param array $data
     * @return bool $update
     */
    public function postTeamProfileUpdate(Request $request)
    {
        try {
            $team = $request->user();
            $data = $request['data'];
            $id = $team->id;

            $update = $this->team_service->postProfileUpdateConvertData($data, $id);
            if (!$update) {
                throw new Exception('チーム情報を更新できませんでした。');
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
     * 選手投稿URL一覧
     * @return array $video_urls
     */
    public function getPlayerVideos()
    {
        try {
            $value = $this->team_service->getPlayerVideos();
            $result = [
                'result_code' => 200,
                'result_message' => 'OK',
                'urls' => $value
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
