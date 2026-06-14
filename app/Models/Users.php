<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Users extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'tb_users';
    protected $primaryKey = 'id_users';
    protected $fillable = ['role', 'nama', 'email', 'password'];
    protected $hidden = ['password'];
}
