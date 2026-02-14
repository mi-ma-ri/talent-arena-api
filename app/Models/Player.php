<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Player extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'players';
    protected $guarded = ['id'];
    protected $hidden = ['password'];
}
