<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserOauth
 *
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserOauth newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserOauth newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserOauth query()
 * @mixin \Eloquent
 */
class UserOauth extends Model
{
    protected $fillable = [
        'nickname',
        'avatar',
        'oauth_type',
        'oauth_id',
        'unionid',
        'credential',
    ];


    public function user()
    {

        return $this->belongsTo(User::class);
    }
    //
}
