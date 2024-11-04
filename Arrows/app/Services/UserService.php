<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;

class UserService
{
    public function update($auth, $username, $email, $name, $image_url = null){
        if($image_url){
            $image_url_buffer = $image_url->store('public/icon');
            $image_url_path = str_replace('public', '/storage', $image_url_buffer);
        }
        $auth->update([
            'username' => $username,
            'email' => $email,
            'name' => $name,
            'image_url' => isset($image_url_path) ? $image_url_path : $auth->image_url,
        ]);
    }

    public function updatePassword($auth, $current_password, $password, $password_confirmation){
        if(!Hash::check($current_password, $auth->password)){
            throw new Exception('パスワードが正しくありません。');
        }
        if($password != $password_confirmation){
            throw new Exception('入力されたパスワードが一致しません。');
        }
        $auth->update([
            'password' => Hash::make($password),
        ]);
    }
}