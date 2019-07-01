<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    //
    protected $fillable = [
        'nickname',
        'avatar',
        'phone',
        'gender',
        'country',
        'province',
        'city',
        'district',
        'user_ext',
    ];
}
