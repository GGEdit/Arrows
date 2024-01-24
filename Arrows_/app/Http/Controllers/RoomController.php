<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;
use App\RoomMember;
use App\Message;

class RoomController extends Controller
{
    private $auth;

    public function __construct(){
        $this->middleware(function ($request, $next){
            $this->auth = \Auth::user();
            return $next($request);
        });
    }

    public function getRoomList(){
        $authId = $this->auth->id;
        $rooms = Room::where('owner_id', $authId)
                    ->orwhereIn('id', function($query) use($authId){
                        $query->from('room_members')
                            ->select('room_id')
                            ->where('user_id', $authId);
                    })->with(['members.user', 'latestMessage'])->get();
        
        return $rooms;
    }
}
