<?php

namespace App\Services;

use App\Consts\CommonConsts;
use App\Models\Player;
use App\Services\CinpherService;

use Illuminate\Support\Facades\Hash;

class PlayerService extends BaseService
{


  /**
   * 【説明】返却する選手情報をコンバート
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
}
