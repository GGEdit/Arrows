<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Consts\RoomType;
use App\Models\Friend;
use App\Models\User;
use App\Models\Room;
use App\Models\RoomMember;

class FriendController extends Controller
{
    private $auth;

    public function __construct(){
        $this->middleware(function ($request, $next){
            $this->auth = \Auth::user();
            return $next($request);
        });
    }

    public function index(){
        return view('/friend/index');
    }

    public function search(Request $request){
        if($request->username == $this->auth->username){
            $errMessage = '自分自身を追加することは出来ません';
            return view('/friend/index', compact('errMessage'));
        }
        $user = User::where('username', $request->username)->first();
        if($user == NULL){
            $errMessage = 'お探しのユーザーは見つかりませんでした';
            return view('/friend/index', compact('errMessage'));
        }
        $isFriend = Friend::where('user_id', $this->auth->id)->where('friend_id', $user->id)->exists();
        return view('/friend/index', compact('user', 'isFriend'));
    }

    public function addMe($username){
        $user = User::where('username', $username)->first();
        if($user == NULL){
            $errMessage = 'お探しのユーザーは見つかりませんでした';
            return view('/friend/add_me', compact('errMessage'));
        }
        $isFriend = false;
        if($this->auth){
            $isFriend = Friend::where('user_id', $this->auth->id)->where('friend_id', $user->id)->exists();
        }
        return view('/friend/add_me', compact('user', 'isFriend'));
    }

    public function store(Request $request){
        $authId = $this->auth->id;
        $user = User::find($request->id);
        if($user == NULL){
            return redirect()->back()->with('error', '存在しないユーザーです');
        }
        if($user->id == $authId){
            return redirect()->back()->with('error', '自分自身を追加することは出来ません');
        }
        $isFriend = Friend::where('user_id', $authId)->where('friend_id', $user->id)->exists();
        if($isFriend){
            return redirect()->back()->with('error', '既に友だちに追加されています');
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
        return redirect()->back()->with('success', '友だちを追加しました');
    }
}
