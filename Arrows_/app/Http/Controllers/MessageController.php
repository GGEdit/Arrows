<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use App\Room;
use App\Events\MessageSent;
use App\Lib\HttpRequest;
use Auth;

class MessageController extends Controller
{
    private $auth;

    public function __construct(){
        $this->middleware(function ($request, $next){
            $this->auth = \Auth::user();
            return $next($request);
        });
    }

    public function get($id){
        $messages = Message::where('room_id', $id)->get();
        return $messages;
    }

    public function store(Request $request){
        $room = Room::find($request->room_id);
        if(!$room){
            return response()->json(['status' => 'error', 'reason' => 'room not found']);
        }

        $message = Message::create([
            'room_id' => $request->room_id,
            'user_id' => $this->auth->id,
            'content' => $request->content,
        ]);

        // ルームの最新メッセージを更新
        $room->update([
            'latest_message_id' => $message->id
        ]);

        // Nodeに通知を飛ばす
        $content = [
            'room_name' => 'ROOM_' . $message->room_id,
            'message' => [
                'id' => $message->id,
                'room_id' => $message->room_id,
                'user_id' => $message->user_id,
                'content' => $message->content,
                'created_at' => $message->created_at
            ]
        ];
        $request = new HttpRequest();
        $request->post('http://localhost:3000/emit_update_chat', $content);

        return response()->json(['message' => 'success']);
    }
}