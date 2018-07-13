<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $table = 'reply';
    protected $primaryKey = 'reply_id';
    public $timestamps = false;
    protected $guarded =[];
    protected $fillable = [
        'message_id', 'be_answered', 'reply_content', 'timestamp', 'reply_img', 'reply_name'
    ];
}
