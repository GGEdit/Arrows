<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 'user_id',
    ];

    public function room(){
        return $this->belongsTo('App\Models\Room', 'room_id');
    }

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
