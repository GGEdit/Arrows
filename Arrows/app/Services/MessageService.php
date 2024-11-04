<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Lib\HttpRequest;
use App\Models\Message;
use App\Models\Room;
use Exception;

class MessageService
{
    public function get($room_id){
        $messages = Message::where('room_id', $room_id)
            ->get()
            ->groupBy('post_date');

        return $messages;
    }

    public function store($user_id, $room_id, $attachment, $content){
        $room = Room::find($room_id);
        if(!$room){
            throw new Exception('room not found');
        }

        $attachmentUrl = NULL;
        $attachment = $attachment;
        if($attachment != NULL){
            $attachmentUrl = Storage::disk('s3')->put(env('S3_OBJECT'), $attachment);
            if(!$attachmentUrl){
                throw new Exception('failed upload attachment');
            }
        }

        $message = Message::create([
            'room_id' => $room_id,
            'user_id' => $user_id,
            'content' => $content,
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
    }

    public function update($message_id, $user_id, $content){
        $message = Message::find($message_id);
        if(!$message){
            throw new Exception('message not found');
        }
        if($message->user_id != $user_id){  
            throw new Exception('permission denied');  
        }
        $message->update([
            'content' => $content
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
    }

    public function delete($message_id, $user_id){
        $message = Message::find($message_id);
        if(!$message){
            throw new Exception('message not found');
        }
        if($message->user_id != $user_id){    
            throw new Exception('permission denied');
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
    }
}