<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'room_id', 'user_id', 'content',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
