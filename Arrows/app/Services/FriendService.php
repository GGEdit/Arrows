<?php

namespace App\Services;

use App\Consts\RoomType;
use App\Models\Friend;
use App\Models\User;
use App\Models\Room;
use App\Models\RoomMember;
use Exception;

class FriendService
{
    public function search($auth, $username){
        if($username == $auth->username || $username == $auth->email){
            throw new Exception('自分自身を追加することは出来ません');
        }
        $user = User::where('username', $username)
            ->orWhere('email', $username)
            ->first();
        if($user == NULL){
            throw new Exception('お探しのユーザーは見つかりませんでした');
        }
        $isFriend = Friend::where('user_id', $auth->id)->where('friend_id', $user->id)->exists();

        return ['user' => $user, 'isFriend' => $isFriend];
    }

    public function addMe($auth, $username){
        $user = User::where('username', $username)
            ->first();
        if($user == NULL){
            throw new Exception('お探しのユーザーは見つかりませんでした');
        }
        $isFriend = false;
        if($auth){
            $isFriend = Friend::where('user_id', $auth->id)
                ->where('friend_id', $user->id)
                ->exists();
        }

        return ['user' => $user, 'isFriend' => $isFriend];
    }

    public function store($auth, $user_id){
        $authId = $auth->id;
        $user = User::find($user_id);
        if($user == NULL){
            throw new Exception('存在しないユーザーです');
        }
        if($user->id == $authId){
            throw new Exception('自分自身を追加することは出来ません');
        }
        $isFriend = Friend::where('user_id', $authId)->where('friend_id', $user->id)->exists();
        if($isFriend){
            throw new Exception('既に友だちに追加されています');
        }
        // 友だちを追加
        Friend::create([
            'user_id' => $authId,
            'friend_id' => $user->id,
        ]);
        // 1対1のルームを作成
        // すでに相手が作成しているか確認
        $room = Room::where('type', RoomType::DIRECT_MESSAGE)
                    ->where('owner_id', $user->id)
                    ->where('id', function($query) use($authId) {
                        $query->from('room_members')
                            ->select('room_id')
                            ->where('user_id', $authId);
                    })->exists();
        if(!$room){
            $room = Room::create([
                'name' => 'ダイレクトメッセージ',
                'type' => RoomType::DIRECT_MESSAGE,
                'owner_id' => $authId
            ]);
            RoomMember::insert(
                [
                    [
                        'room_id' => $room->id,
                        'user_id' => $authId
                    ],
                    [
                        'room_id' => $room->id,
                        'user_id' => $user->id
                    ]
                ]
            );
        }
    }
}