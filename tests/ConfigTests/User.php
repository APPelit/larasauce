<?php

namespace Tests\ConfigTests;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Eloquent\Model
 * @mixin \Illuminate\Database\Query\Builder
 */
class User extends Authenticatable
{
    use Notifiable;

    public $incrementing = false;
    protected $fillable = [
        'id',
        'name',
        'username',
        'email',
        'password',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
