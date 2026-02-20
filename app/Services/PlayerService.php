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
}
