<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'owner_id', 'latest_message_id',
    ];

    public function members(){
        return $this->hasMany('App\Models\RoomMember', 'room_id', 'id');
    }

    public function latestMessage(){
        return $this->belongsTo('App\Models\Message', 'latest_message_id');
    }
}
