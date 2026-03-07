<?php

namespace App\Services;

use App\Consts\CommonConsts;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Support\Facades\Hash;

class LoginService extends BaseService
{


  /**
   * 【説明】存在する選手情報を返します。存在しなければNullを返す。
   * @param string $email
   * @param string $password
   * @return Player|null
   */
  public function auth(string $email, string $password): ?Player
  {
    $cinpher_service = new CinpherService();
    $ms_hash = $cinpher_service->getDecryptParam($email)->ms_hash;

    $player = Player::where('ms_hash', $ms_hash)
      ->where('players_status', CommonConsts::IS_MEMBER)
      ->first();

    if ($player && Hash::check($password, $player->password)) {
      return $player;
    }

    return null;
  }

  /**
   * 【説明】存在するチーム情報を返します。存在しなければNullを返す。
   * @param string $email
   * @param string $password
   * @return Team|null
   */
  public function teamAuth(string $email, string $password): ?Team
  {
    $cinpher_service = new CinpherService();
    $ms_hash = $cinpher_service->getDecryptParam($email)->ms_hash;

    $team = Team::where('ms_hash', $ms_hash)
      ->where('teams_status', CommonConsts::IS_TEAM)
      ->first();

    if ($team && Hash::check($password, $team->password)) {
      return $team;
    }
    return null;
  }
}
