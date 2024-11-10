<?php

namespace App\Services;

use App\Consts\MessageType;
use App\Lib\HttpRequest;
use App\Models\Meet;
use App\Models\Message;
use App\Models\Room;
use Exception;

class MeetService
{
    public function notifyConference($user_id, $room_id, $meet_name){
        // Model作成
        $meet = Meet::create([
            'owner_id' => $user_id,
            'room_id' => $room_id,
            'meet_name' => $meet_name
        ]);

        // 作成したmeetを取得
        $meet = Meet::with(['owner'])
            ->find($meet->id);
        
        // オンライン会議メッセージを作成
        $message = Message::create([
            'room_id' => $room_id,
            'user_id' => $user_id,
            'content' => '📞 オンライン会議を開始しました',
            'message_type' => MessageType::SYSTEM_MESSAGE
        ]);

        // ルームModel更新
        $room = Room::find($meet->room_id);
        $room->update([
            'opening_meet_id' => $meet->id,
            'latest_message_id' => $message->id
        ]);

        $request = new HttpRequest();
        // Nodeにメッセージ投稿の通知を飛ばす
        $content = [
            'room_name' => 'ROOM_' . $message->room_id,
            'message' => [
                'id' => $message->id,
                'room_id' => $message->room_id,
                'user_id' => $message->user_id,
                'content' => $message->content,
                'attachment_url' => $message->attachment_url,
                'message_type' => $message->message_type,
                'created_at' => $message->created_at,
                'updated_at' => $message->updated_at,
                'post_date' => $message->post_date,
            ]
        ];
        $request->post_json(config('app.nsocket_server') . '/emit_create_chat', $content, [
            'Content-Type' => 'text/html'
        ]);

        // Nodeにオンライン会議開始の通知を飛ばす
        $content = [
            'room_name' => 'ROOM_' . $meet->room_id,
            'meet' => $meet
        ];
        $request->post_json(config('app.nsocket_server') . '/emit_notify_conference', $content, [
            'Content-Type' => 'text/html'
        ]);
    }

    public function notifyTerminateConference($user_id, $room_id, $meet_name){
        $meet = Meet::where('room_id', $room_id)
            ->where('meet_name', $meet_name)
            ->first();
        if(!$meet){
            throw new \Exception('meet not found');
        }
        if($meet->owner_id != $user_id){
            throw new Exception('permission denied');       
        }
        
        $room = Room::find($meet->room_id);
        if($room == NULL){
            throw new Exception('room not found');
        }
        if($room->opening_meet_id == NULL){
            throw new Exception('meet already terminated');
        }

        // オンライン会議終了メッセージを作成
        $message = Message::create([
            'room_id' => $room_id,
            'user_id' => $user_id,
            'content' => '📞 オンライン会議が終了しました',
            'message_type' => MessageType::SYSTEM_MESSAGE
        ]);

        // ルームModel更新
        $room->update([
            'opening_meet_id' => NULL,
            'latest_message_id' => $message->id
        ]);

        $request = new HttpRequest();
        // Nodeにメッセージ投稿の通知を飛ばす
        $content = [
            'room_name' => 'ROOM_' . $message->room_id,
            'message' => [
                'id' => $message->id,
                'room_id' => $message->room_id,
                'user_id' => $message->user_id,
                'content' => $message->content,
                'attachment_url' => $message->attachment_url,
                'message_type' => $message->message_type,
                'created_at' => $message->created_at,
                'updated_at' => $message->updated_at,
                'post_date' => $message->post_date,
            ]
        ];
        $request->post_json(config('app.nsocket_server') . '/emit_create_chat', $content, [
            'Content-Type' => 'text/html'
        ]);

        // Nodeにオンライン会議終了の通知を飛ばす
        $content = [
            'room_name' => 'ROOM_' . $meet->room_id,
            'meet' => $meet
        ];
        $request->post_json(config('app.nsocket_server') . '/emit_notify_terminate_conference', $content, [
            'Content-Type' => 'text/html'
        ]);
    }
}
