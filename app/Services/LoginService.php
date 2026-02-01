<?php

namespace App\Services;

use Exception;
use stdClass;
use Carbon\Carbon;
use Throwable;
use App\Consts\CommonConsts;
use App\Models\Player;
use App\Models\AuthKey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginService extends BaseService
{


  /**
   * 【説明】指定したテーブルに認証キーが存在するかどうかを返す
   * @param string $table
   * @param string $key
   * @return bool
   */
  public function authenticate(string $email, string $password): bool
  {
    $cinpher_service = new CinpherService();
    $ms_hash = $cinpher_service->getDecryptParam($email)->ms_hash;

    $player = DB::table('players AS p')
      ->where('p.ms_hash', $ms_hash)
      ->where('p.players_status', CommonConsts::IS_MEMBER)
      ->first();

    if (!$player) {
      return false;
    }

    return Hash::check($password, $player->password);
  }
}
