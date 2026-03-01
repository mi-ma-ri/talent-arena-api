<?php

namespace App\Services;

use App\Consts\CommonConsts;
use App\Models\Player;
use App\Models\Team;
use App\Models\AuthKey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use stdClass;
use Carbon\Carbon;
use Throwable;
use Illuminate\Support\Facades\Log;


class RegisterService extends BaseService
{

  private array $auth_key_table = [
    'auth_keys'
  ];

  /**
   * 【説明】仮登録用の認証キーを生成する
   * @param string $email
   * @param int $status
   * @param int $subject_type
   * @return string
   */
  public function getRegisterAuthKey(string $email, int $status, int $subject_type): object
  {
    try {
      $cinpher_service = new CinpherService();
      $result = new stdClass;

      # 暗号化キーの生成
      $encrypt_param = $cinpher_service->getDecryptParam($email);

      # 仮登録済みかどうかの判定
      $encrypt_param = $this->_changeEncryptParam($encrypt_param, $email, $status, $subject_type);

      # ユーザー情報を取得 選手・チーム共通
      DB::beginTransaction();
      $subject_tmp = $this->_getSubjectTmp($encrypt_param, $status, $subject_type);

      # 仮登録毎にauth_keys.auth_keyの削除
      AuthKey::where('subject_id', $subject_tmp->id)
        ->whereNull('auth_date')
        ->delete();

      # auth_keyの生成
      $key = AuthKey::create([
        'subject_id' => $subject_tmp->id,
        'subject_type' => $subject_type,
        'auth_key' => $this->_createKey('auth_keys'),
        'expire_date' => (new Carbon())->addDay()->format('Y-m-d H:i:s')
      ]);

      if (!$key) {
        throw new Exception('auth_key insert error');
      }

      $result->key = $key->auth_key;
      $result->subject_id = $key->subject_id;

      DB::commit();
      return $result;
    } catch (Exception $e) {
      DB::rollBack();
      throw new Exception('認証キーの生成に失敗しました。理由: ' . $e->getMessage());
    }
  }

  public function findByEmail(string $email, int $subject_type): mixed
  {
    $cinpher_service = new CinpherService();
    $ms_hash = $cinpher_service->getDecryptParam($email)->ms_hash;

    # 仮登録情報を取得
    if ($subject_type == CommonConsts::SUBJECT_TYPE_PLAYERS) {
      return DB::table('players AS p')
        ->where('p.ms_hash', $ms_hash)
        ->where('p.players_status', CommonConsts::IS_MEMBER)
        ->first();
    } elseif ($subject_type == CommonConsts::SUBJECT_TYPE_TEAMS) {
      return DB::table('teams AS t')
        ->where('t.ms_hash', $ms_hash)
        ->where('t.teams_status', CommonConsts::IS_TEAM)
        ->first();
    }

    return null;
  }

  /**
   * 【説明】既に仮登録済みのメールアドレスの場合、DBから取得した情報で暗号化しなおす。未認証であれば生成した暗号化情報をそのまま使用する。
   * @param string $email
   * @param int $is_tmp_member
   * @return string
   */
  private function _changeEncryptParam(stdClass $encrypt_param, string $email, int $status, int $subject_type): stdClass
  {
    $cinpher_service = new CinpherService();

    # 仮登録情報を取得
    if ($subject_type == CommonConsts::SUBJECT_TYPE_PLAYERS) {
      $uncertified = DB::table('players AS p')
        ->where('p.players_status', $status)
        ->where('p.ms_hash', $encrypt_param->ms_hash)
        ->first();
    } elseif ($subject_type == CommonConsts::SUBJECT_TYPE_TEAMS) {
      $uncertified = DB::table('teams AS t')
        ->where('t.teams_status', $status)
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

  /**
   * 【説明】仮登録メールアドレス登録済みチェック
   * @param stdClass $encrypt_param
   * @param int $status
   * @param int $subject_type
   * @return Player|Team
   */
  private function _getSubjectTmp(stdClass $encrypt_param, int $status, int $subject_type): Player|Team
  {

    $is_player = DB::table('players')
      ->where('ms_hash', $encrypt_param->ms_hash)
      ->where('players_status', CommonConsts::IS_MEMBER)
      ->exists();

    if ($is_player) {
      throw new Exception('このメールアドレスは既に登録されています。');
    }

    $is_team = DB::table('teams')
      ->where('ms_hash', $encrypt_param->ms_hash)
      ->where('teams_status', CommonConsts::IS_TEAM)
      ->exists();

    if ($is_player) {
      throw new Exception('このメールアドレスは既に登録されています。');
    }

    $member = $this->registerTempUser($subject_type, $status, $encrypt_param);

    return $member;
  }

  private function registerTempUser(int $subject_type, int $status, stdClass $encrypt_param): Player|Team
  {
    try {
      DB::beginTransaction();
      # 仮登録情報をplayersテーブルに保存
      if ($subject_type == CommonConsts::SUBJECT_TYPE_PLAYERS) {
        $member = Player::create([
          'players_status' => $status,
          'ms' => $encrypt_param->ms,
          'ms_hash' => $encrypt_param->ms_hash,
          'unique_salt' => $encrypt_param->unique_salt,
          'ms_v' => $encrypt_param->ms_v,
        ]);
      } elseif ($subject_type == CommonConsts::SUBJECT_TYPE_TEAMS) {
        $member = Team::create([
          'teams_status' => $status,
          'ms' => $encrypt_param->ms,
          'ms_hash' => $encrypt_param->ms_hash,
          'unique_salt' => $encrypt_param->unique_salt,
          'ms_v' => $encrypt_param->ms_v,
        ]);
      }
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

  /**
   * 【説明】auth_keysテーブルの認証日時を更新する（共通処理）
   * @param int $subject_id
   * @return void
   */
  private function _updateAuthKey(int $subject_id): void
  {
    DB::table('auth_keys')
      ->where('subject_id', $subject_id)
      ->update([
        'auth_date' => (new Carbon())->format('Y-m-d H:i:s'),
        'updated_at' => (new Carbon())->format('Y-m-d H:i:s'),
      ]);
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
  public function containsKey(string $table): bool
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

  /**
   * 【説明】認証処理中のプレイヤー情報を取得する
   * @param array $params
   * @return object
   */
  public function getRegisterPlayer(string $key): object
  {
    try {
      $cinpher_service = new CinpherService();

      # 仮登録情報を取得
      $player = DB::table('auth_keys AS au')
        ->join('players AS p', 'au.subject_id', 'p.id')
        ->select('p.players_status', 'p.ms', 'p.unique_salt', 'p.ms_v', 'au.subject_id')
        ->where('auth_key', $key)
        ->whereNull('auth_date')
        ->where('expire_date', '>', (new Carbon())->format('Y-m-d H:i:s'))
        ->first();

      if (empty($player)) {
        throw new Exception('プレイヤー情報を取得できません。');
      }

      $player->email = $cinpher_service->decrypt(
        $player->ms,
        $player->unique_salt,
        $player->ms_v
      );

      return $player;
    } catch (Throwable $e) {
      throw new Exception($e->getMessage());
    }
  }

  /**
   * 【説明】認証処理中のチーム情報を取得する
   * @param array $params
   * @return object
   */
  public function getRegisterTeam(string $key): object
  {
    try {
      $cinpher_service = new CinpherService();

      # 仮登録情報を取得
      $team = DB::table('auth_keys AS au')
        ->join('teams AS t', 'au.subject_id', 't.id')
        ->select('t.teams_status', 't.ms', 't.unique_salt', 't.ms_v', 'au.subject_id')
        ->where('auth_key', $key)
        ->whereNull('auth_date')
        ->where('expire_date', '>', (new Carbon())->format('Y-m-d H:i:s'))
        ->first();

      if (empty($team)) {
        throw new Exception('プレイヤー情報を取得できません。');
      }

      $team->email = $cinpher_service->decrypt(
        $team->ms,
        $team->unique_salt,
        $team->ms_v
      );

      return $team;
    } catch (Throwable $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function postJoin(array $params, $status): void
  {
    try {
      DB::beginTransaction();
      // playersテーブルの更新
      DB::table('players')
        ->where('id', $params['subject_id'])
        ->update([
          'players_status' => $status,
          'first_name' => $params['first_name'],
          'second_name' => $params['second_name'],
          'affiliated_team' => $params['affiliated_team'],
          'password' => Hash::make($params['password']),
          'position' => $params['position'],
          'birth_date' => $params['birth_date'],
        ]);

      // auth_keysテーブルの更新
      $this->_updateAuthKey($params['subject_id']);

      DB::commit();
    } catch (Throwable $e) {
      DB::rollBack();
      throw new Exception('登録に失敗しました。理由: ' . $e->getMessage());
    }
  }

  public function postTeamJoin(array $params, $status): void
  {
    try {
      DB::beginTransaction();
      // playersテーブルの更新
      DB::table('teams')
        ->where('id', $params['subject_id'])
        ->update([
          'teams_status' => $status,
          'teams_name' => $params['teams_name'],
          'location' => $params['location'],
          'website' => $params['website'],
          'teams_policy' => $params['teams_policy'],
          'schedule' => $params['schedule'],
          'ob_affiliation' => $params['ob_affiliation'],
        ]);

      // auth_keysテーブルの更新
      $this->_updateAuthKey($params['subject_id']);

      DB::commit();
    } catch (Throwable $e) {
      DB::rollBack();
      throw new Exception('登録に失敗しました。理由: ' . $e->getMessage());
    }
  }
}
