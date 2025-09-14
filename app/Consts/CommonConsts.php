<?php

namespace App\Consts;

class CommonConsts
{
  /*
    |--------------------------------------------------------------------------
    | user_status(登録処理が共通のため選手とチーム共通)
    |--------------------------------------------------------------------------
  */
  public const IS_TMP_MEMBER = 0;
  public const IS_MEMBER = 1;
  public const IS_LEAVE_MATCH_MEMBER = 2;
  public const IS_LEAVE_MEMBER = 3;

  /*
    |--------------------------------------------------------------------------
    | user_type
    |--------------------------------------------------------------------------
  */
  public const USER_TYPE_PLAYERS = 0; // 選手
  public const USER_TYPE_TEAMS = 1; // スカウトチーム
}
