<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthKey extends Model
{
    use HasFactory;

    protected $table = 'auth_keys';

    protected $guarded = ['id'];

    
}
