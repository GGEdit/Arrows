<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Lib\HttpRequest;
use App\Models\Message;
use App\Models\Room;

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
        $messages = Message::where('room_id', $id)->get()->groupBy('post_date');
        return $messages;
    }

    public function store(Request $request){
        $room = Room::find($request->room_id);
        if(!$room){
            return response()->json(['status' => 'error', 'reason' => 'room not found']);
        }

        $attachmentUrl = NULL;
        $attachment = $request->file('attachment');
        if($attachment != NULL){
            $attachmentUrl = Storage::disk('s3')->put(env('S3_OBJECT'), $attachment);
            if(!$attachmentUrl){
                return response()->json(['message' => 'failed upload attachment']);
            }
        }

        $message = Message::create([
            'room_id' => $request->room_id,
            'user_id' => $this->auth->id,
            'content' => $request->content,
            'attachment_url' => $attachmentUrl,
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
                'attachment_url' => $message->attachment_url,
                'created_at' => $message->created_at,
                'updated_at' => $message->updated_at,
                'post_date' => $message->post_date,
            ]
        ];
        $request = new HttpRequest();
        $request->post_json(config('app.nsocket_server') . '/emit_create_chat', $content, [
            'Content-Type' => 'text/html'
        ]);

        return response()->json(['message' => 'success']);
    }

    public function update(Request $request, $id){
        $message = Message::find($id);
        if(!$message){
            return response()->json(['status' => 'error', 'reason' => 'message not found']);
        }
        if($message->user_id != $this->auth->id){
            return response()->json(['status' => 'error', 'reason' => 'permission denied']);            
        }
        $message->update([
            'content' => $request->content
        ]);

        // Nodeに通知を飛ばす
        $content = [
            'room_name' => 'ROOM_' . $message->room_id,
            'message' => [
                'id' => $message->id,
                'room_id' => $message->room_id,
                'user_id' => $message->user_id,
                'content' => $message->content,
                'attachment_url' => $message->attachment_url,
                'created_at' => $message->created_at,
                'updated_at' => $message->updated_at,
                'post_date' => $message->post_date,
            ]
        ];
        $request = new HttpRequest();
        $request->post_json(config('app.nsocket_server') . '/emit_update_chat', $content, [
            'Content-Type' => 'text/html'
        ]);

        return response()->json(['message' => 'success']);
    }

    public function delete($id){
        $message = Message::find($id);
        if(!$message){
            return response()->json(['status' => 'error', 'reason' => 'message not found']);
        }
        if($message->user_id != $this->auth->id){
            return response()->json(['status' => 'error', 'reason' => 'permission denied']);            
        }

        // メッセージ削除
        $message->delete();

        // 最新メッセージに登録されている場合は更新
        $room = Room::where('id', $message->room_id)
            ->first();
        if($room->latest_message_id == $message->id){
            $latestMessage = Message::where('room_id', $room->id)
                ->orderBy('created_at', 'desc')
                ->first();
            $room->update([
                'latest_message_id' => $latestMessage ? $latestMessage->id : NULL
            ]);
        }

        // Nodeに通知を飛ばす
        $content = [
            'room_id' => $message->room_id,
            'room_name' => 'ROOM_' . $message->room_id,
            'message_id' => $message->id
        ];
        if(isset($latestMessage) && $latestMessage){
            $content['latest_message'] = [
                'id' => $latestMessage->id,
                'room_id' => $latestMessage->room_id,
                'user_id' => $latestMessage->user_id,
                'content' => $latestMessage->content,
                'attachment_url' => $latestMessage->attachment_url,
                'created_at' => $latestMessage->created_at
            ];
        }
        $request = new HttpRequest();
        $request->post_json(config('app.nsocket_server') . '/emit_delete_chat', $content, [
            'Content-Type' => 'text/html'
        ]);

        return response()->json(['status' => 'ok']);
    }
}