<?php

namespace App\Services;

use App\Consts\CommonConsts;
use App\Models\Player;
use App\Services\CinpherService;
use Exception;


use Illuminate\Support\Facades\Hash;

class PlayerService extends BaseService
{


  /**
   * 返却する選手情報をコンバート
   * @param Player $player
   * @return array
   */
  public function getProfileConvertData(Player $player): array
  {
    $cinpher_service = new CinpherService();

    return [
      'address' => $cinpher_service->decrypt($player->ms, $player->unique_salt, $player->ms_v),
      'first_name' => $player->first_name,
      'second_name' => $player->second_name,
      'affiliated_team' => $player->affiliated_team,
      'position' => $player->position,
      'birth_date' => $player->birth_date
    ];
  }

  /**
   * 選手情報更新処理
   * @param string $address
   * @param string $position
   * @param int $id
   * @return bool
   */
  public function postProfileUpdateConvertData(string $address, $position, int $id): bool
  {
    $cinpher_service = new CinpherService();
    $encrypt_param = $cinpher_service->getDecryptParam($address);

    # 該当の選手情報を取得
    $player = Player::find($id);
    if (!$player) {
      return false;
    }

    # メールアドレス、ポジションを更新
    $player->update([
      'ms' => $encrypt_param->ms,
      'unique_salt' => $encrypt_param->unique_salt,
      'ms_v' => $encrypt_param->ms_v,
      "position" => $position,
    ]);

    return True;
  }

  /**
   * 動画URL登録処理
   * @param Player $player
   * @param mixed $request
   * @return bool
   */
  public function postHandleUrl(Player $player, mixed $request): bool
  {
    $postData = $request->postData;

    $player->playerVideos()->create([
      'sns_url_1' => $postData['url1'],
      'sns_url_2' => $postData['url2'],
      'sns_url_3' => $postData['url3'],
      'description' => $postData['description'],
    ]);

    return true;
  }

  /**
   * 動画URL一覧取得
   * @param Player $player
   * @return array
   */
  public function getUrl(Player $player): array
  {
    $videoData = $player->playerVideos()
      ->orderBy('created_at', 'desc')
      ->get()
      ->map(function ($video) {
        return [
          'id' => $video->id,
          'player_id' => $video->player_id,
          'sns_url_1' => $video->sns_url_1,
          'sns_url_2' => $video->sns_url_2,
          'sns_url_3' => $video->sns_url_3,
          'description' => $video->description,
          'created_at' => $video->created_at->format('Y-m-d H:i'),
        ];
      })
      ->toArray();

    return $videoData;
  }
}
