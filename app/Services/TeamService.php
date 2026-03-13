<?php

namespace App\Services;

use App\Models\Team;
use App\Models\PlayerVideo;
use App\Services\CinpherService;

class TeamService extends BaseService
{
  /**
   * 返却する選手情報をコンバート
   * @param Team $team
   * @return array
   */
  public function getProfileConvertData(Team $team): array
  {
    $cinpher_service = new CinpherService();

    return [
      'address' => $cinpher_service->decrypt($team->ms, $team->unique_salt, $team->ms_v),
      'teams_name' => $team->teams_name,
      'website' => $team->website,
      'location' => $team->location,
      'teams_policy' => $team->teams_policy,
      'schedule' => $team->schedule,
      'ob_affiliation' => $team->ob_affiliation
    ];
  }

  /**
   * チーム情報更新処理
   * @param string $address
   * @param string $position
   * @param int $id
   * @return bool
   */
  public function postProfileUpdateConvertData(array $data, int $id): bool
  {
    $cinpher_service = new CinpherService();
    $encrypt_param = $cinpher_service->getDecryptParam($data['address']);

    # 該当の選手情報を取得
    $team = Team::find($id);
    if (!$team) {
      return false;
    }

    # メールアドレス、ポジションを更新
    $team->update([
      'ms' => $encrypt_param->ms,
      'unique_salt' => $encrypt_param->unique_salt,
      'ms_v' => $encrypt_param->ms_v,
      'location' => $data['location'],
      'website' => $data['website'],
      'teams_policy' => $data['teams_policy'],
      'schedule' => $data['schedule'],
      'ob_affiliation' => $data['ob_affiliation']
    ]);

    return True;
  }

  /**
   * 選手投稿URL一覧
   * @return array $value
   */
  public function getPlayerVideos(): array
  {
    # 選手が投稿したURL一覧を取得、併せて選手情報も取得
    $value = PlayerVideo::with('player:id,first_name,second_name,position,affiliated_team,birth_date')
      ->orderBy('created_at', 'desc')
      ->get()
      ->toArray();

    return $value;
  }
}
