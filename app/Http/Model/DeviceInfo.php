<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class DeviceInfo extends Model
{
    protected $table = 'visit_info';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
