<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class IpInfo extends Model
{
    protected $table = 'ip_address';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded =[];
}
