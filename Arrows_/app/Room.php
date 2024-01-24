<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'name', 'type', 'owner_id', 'latest_message_id',
    ];

    public function members(){
        return $this->hasMany('App\RoomMember', 'room_id', 'id');
    }

    public function latestMessage(){
        return $this->belongsTo('App\Message', 'latest_message_id');
    }
}
