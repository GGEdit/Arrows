<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomMember extends Model
{
    protected $fillable = [
        'room_id', 'user_id',
    ];

    public function room(){
        return $this->belongsTo('App\Room', 'room_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}