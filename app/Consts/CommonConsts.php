<?php

namespace App\Consts;

class CommonConsts
{
  /*
    |--------------------------------------------------------------------------
    | status(登録処理が共通のため選手とチーム共通)
    |--------------------------------------------------------------------------
  */
  public const IS_TMP_MEMBER = 0;
  public const IS_MEMBER = 1;
  public const IS_LEAVE_MATCH_MEMBER = 2;
  public const IS_LEAVE_MEMBER = 3;

  /*
    |--------------------------------------------------------------------------
    | subject_type
    |--------------------------------------------------------------------------
  */
  public const SUBJECT_TYPE_PLAYERS = 0; // 選手
  public const SUBJECT_TYPE_TEAMS = 1; // スカウトチーム
}
