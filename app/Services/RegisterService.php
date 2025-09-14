<?php

namespace App\Services;

use App\Consts\CommonConsts;
use App\Models\Player;
use App\Models\AuthKey;
use Illuminate\Support\Facades\DB;
use Exception;
use stdClass;
use Carbon\Carbon;

class RegisterService extends BaseService
{

  private array $auth_key_table = [
    'auth_keys'
  ];

  /**
   * 【説明】仮登録用の認証キーを生成する
   * @param string $email
   * @param int $user_status
   * @param int $user_type
   * @return string
   */
  public function getRegisterAuthKey(string $email, int $user_status, int $user_type): object
  {
    try {
      $cinpher_service = new CinpherService();
      $result = new stdClass;

      # 暗号化キーの生成
      $encrypt_param = $cinpher_service->getDecryptParam($email);

      # ユーザーロールの評価
      $is_roll = false;
      $user_type = $is_roll ? CommonConsts::USER_TYPE_TEAMS : CommonConsts::USER_TYPE_PLAYERS;

      # 仮登録済みかどうかの判定
      $encrypt_param = $this->_changeEncryptParam($encrypt_param, $email, $user_status, $user_type);

      # ユーザー情報を取得
      DB::beginTransaction();
      $member = $this->_getMember($encrypt_param, $user_status, $user_type);

      # 仮登録毎にauth_keys.auth_keyの削除
      AuthKey::where('id', $member->id)
        ->whereNull('auth_date')
        ->delete();

      # auth_keyの生成
      $key = AuthKey::create([
        'role_id' => $member->id,
        'user_type' => $user_type,
        'auth_key' => $this->_createKey('auth_keys'),
        'expire_date' => (new Carbon())->addDay()->format('Y-m-d H:i:s')
      ]);

      if (!$key) {
        throw new Exception('auth_key insert error');
      }

      $result->key = $key->auth_key;
      $result->role_id = $key->id;

      DB::commit();
      return $result;
    } catch (Exception $e) {
      DB::rollBack();
      throw new Exception('認証キーの生成に失敗しました。理由: ' . $e->getMessage());
    }
  }

  public function findByEmail(string $email): mixed
  {
    $cinpher_service = new CinpherService();
    $ms_hash = $cinpher_service->getDecryptParam($email)->ms_hash;

    # 仮登録情報を取得
    return DB::table('players AS p')
      ->where('p.ms_hash', $ms_hash)
      ->where('p.players_status', CommonConsts::IS_MEMBER)
      ->first();
  }

  /**
   * 【説明】既に仮登録済みのメールアドレスの場合、DBから取得した情報で暗号化しなおす。未認証であれば生成した暗号化情報をそのまま使用する。
   * @param string $email
   * @param int $is_tmp_member
   * @return string
   */
  private function _changeEncryptParam(stdClass $encrypt_param, string $email, int $user_status, int $user_type): stdClass
  {
    $cinpher_service = new CinpherService();

    # 仮登録情報を取得
    if ($user_type == CommonConsts::USER_TYPE_PLAYERS) {
      $uncertified = DB::table('players AS p')
        ->where('p.players_status', $user_status)
        ->where('p.ms_hash', $encrypt_param->ms_hash)
        ->first();
    } elseif ($user_type == CommonConsts::USER_TYPE_TEAMS) {
      $uncertified = DB::table('teams AS t')
        ->where('t.teams_status', $user_status)
        ->where('t.ms_hash', $encrypt_param->ms_hash)
        ->first();
    } else {
      $uncertified = null;
    }

    # DBから取得した情報が空の場合、生成した暗号化情報をそのまま使用する
    if (empty($uncertified)) {
      return $encrypt_param;
    }

    # DBから取得した暗号情報に上書きする（存在する場合）
    $encrypt_param->unique_salt = $uncertified->unique_salt;
    $encrypt_param->ms_v = $uncertified->ms_v;
    $encrypt_param->ms = $cinpher_service->encypt(
      $email,
      $encrypt_param->unique_salt,
      $encrypt_param->ms_v
    );

    return $encrypt_param;
  }

  private function _getMember(stdClass $encrypt_param, int $user_status, int $user_type): Player
  {

    if ($user_type == CommonConsts::USER_TYPE_PLAYERS) {
      $registred = DB::table('players AS p')
        ->where('p.ms_hash', $encrypt_param->ms_hash)
        ->get();
    }

    if ($user_type == CommonConsts::USER_TYPE_TEAMS) {
      $registred = DB::table('teams AS t')
        ->where('t.ms_hash', $encrypt_param->ms_hash)
        ->first();
    }

    foreach ($registred as $row) {
      $status = $user_type == CommonConsts::USER_TYPE_PLAYERS
        ? $row->players_status
        : $row->teams_status;

      if ($status == CommonConsts::IS_MEMBER) {
        throw new Exception('このメールアドレスは既に登録されています。');
      }
    }

    $member = $this->registerTempUser($user_type, $user_status, $encrypt_param);

    return $member;
  }

  private function registerTempUser(int $user_type, int $user_status, stdClass $encrypt_param): Player
  {
    try {
      DB::beginTransaction();
      # 仮登録情報をplayersテーブルに保存 111
      if ($user_type == CommonConsts::USER_TYPE_PLAYERS) {
        $member = Player::create([
          'players_status' => $user_status,
          'ms' => $encrypt_param->ms,
          'ms_hash' => $encrypt_param->ms_hash,
          'unique_salt' => $encrypt_param->unique_salt,
          'ms_v' => $encrypt_param->ms_v,
        ]);
      }

      // # 仮登録情報をteamsテーブルに保存
      // if ($user_type == CommonConsts::USER_TYPE_TEAMS) {
      //   $member = DB::table('teams')->insert([
      //     'team_status' => $user_status,
      //     'ms' => $encrypt_param->ms,
      //     'ms_hash' => $encrypt_param->ms_hash,
      //     'unique_salt' => $encrypt_param->unique_salt,
      //     'ms_v' => $encrypt_param->ms_v,
      //   ]);
      // }
      if (!$member) {
        throw new Exception('member insert error');
      }

      DB::commit();
      return $member;
    } catch (Exception $e) {
      DB::rollBack();
      throw new Exception($e->getMessage());
    }
  }

  private function _createKey(string $table): string
  {
    if (!$this->containsKey($table)) {
      throw new Exception('ユニークキーを生成できないテーブルです。');
    }

    $cinpher_service = new CinpherService();
    $key = $cinpher_service->hash_hmac();

    if (DB::table($table)->where('auth_key', $key)->exists()) {
      return $this->_createKey($table);
    }

    return $key;
  }

  /**
   * 【説明】auth_keyカラムが存在するテーブルかどうかを返す
   * @param string $table
   * @return bool
   */
  private function containsKey(string $table): bool
  {
    return in_array($table, $this->auth_key_table);
  }

  /**
   * 【説明】指定したテーブルに認証キーが存在するかどうかを返す
   * @param string $table
   * @param string $key
   * @return bool
   */
  public function existsKey(string $table, string $key): bool
  {
    return DB::table($table)
      ->where('auth_key', $key)
      ->whereNull('auth_date')
      ->where('expire_date', '>', (new Carbon())->format('Y-m-d H:i:s'))
      ->exists();
  }
}
