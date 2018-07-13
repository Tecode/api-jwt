<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class LoginInfo extends Model
{
    protected $table = 'three_party';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
}
