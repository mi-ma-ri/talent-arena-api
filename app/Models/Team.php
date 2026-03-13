<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Team extends Authenticatable
{
  use HasApiTokens;

  protected $table = 'teams';
  protected $guarded = ['id'];
  protected $hidden = ['password'];

  public function players()
  {
    return $this->belongsTo(Player::class);
  }
}
