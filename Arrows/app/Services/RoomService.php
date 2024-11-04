<?php

namespace App\Services;

use App\Models\Room;

class RoomService
{
    public function getRoomList($user_id){
        $rooms = Room::where('owner_id', $user_id)
            ->orwhereIn('id', function($query) use($user_id){
                $query->from('room_members')
                    ->select('room_id')
                    ->where('user_id', $user_id);
            })
            ->with([
                'members.user', 'latestMessage', 'openingMeet', 'openingMeet.owner'
            ])
            ->get();
        
        return $rooms;
    }
}