<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerVideo extends Model
{
    use HasFactory;

    protected $table = 'player_videos';
    protected $guarded = ['id'];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
