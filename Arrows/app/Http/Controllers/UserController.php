<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $auth;

    public function __construct(){
        $this->middleware(function ($request, $next){
            $this->auth = \Auth::user();
            return $next($request);
        });
    }

    public function index(){
        $user = $this->auth;
        return view('/account/index', compact('user'));
    }

    public function update(Request $request){
        if($request->hasFile('image_url')){
            $image_url_buffer = $request->image_url->store('public/icon');
            $image_url_path = str_replace('public', '/storage', $image_url_buffer);
        }
        $this->auth->update([
            'username' => $request->username,
            'email' => $request->email,
            'name' => $request->name,
            'image_url' => isset($image_url_path) ? $image_url_path : $this->auth->image_url,
        ]);
        return redirect('/account')->with('success', 'アカウント情報を正常に更新しました。');
    }

    public function updatePassword(Request $request){
        if(!Hash::check($request->current_password, $this->auth->password)){
            return redirect('/account')->with('error', 'パスワードが正しくありません。');
        }
        if($request->password != $request->password_confirmation){
            return redirect('/account')->with('error', '入力されたパスワードが一致しません。');
        }
        $this->auth->update([
            'password' => Hash::make($request->password),
        ]);
        return redirect('/account')->with('success', 'パスワードを正常に更新しました。');
    }
}
