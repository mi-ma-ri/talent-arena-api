<?php

namespace App\Consts;

class CommonConsts
{
  /*
    |--------------------------------------------------------------------------
    | 選手登録ステータス
    |--------------------------------------------------------------------------
  */
  public const IS_TMP_MEMBER = 0;
  public const IS_MEMBER = 1;
  public const IS_MATCHING_LEAVED = 2; // マッチング退会
  public const IS_NORMAL_LEAVED   = 3; // 通常退会

  /*
    |--------------------------------------------------------------------------
    | チーム登録ステータス
    |--------------------------------------------------------------------------
  */
  public const IS_TMP_TEAM = 0; // 仮登録
  public const IS_TEAM = 1; // 登録済み
  public const IS_LEAVED_TEAM = 2; // 退会

  /*
    |--------------------------------------------------------------------------
    | subject_type
    |--------------------------------------------------------------------------
  */
  public const SUBJECT_TYPE_PLAYERS = 0; // 選手
  public const SUBJECT_TYPE_TEAMS = 1; // スカウトチーム
}
