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
}
