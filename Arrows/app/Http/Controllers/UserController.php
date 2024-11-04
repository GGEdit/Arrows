<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Exception;

class UserController extends Controller
{
    private $auth;
    private $userService;

    public function __construct(UserService $userService){
        $this->middleware(function ($request, $next){
            $this->auth = \Auth::user();
            return $next($request);
        });
        $this->userService = new UserService();
    }

    public function index(){
        $user = $this->auth;
        return view('/account/index', compact('user'));
    }

    public function update(Request $request){
        try{
            $this->userService->update(
                $this->auth,
                $request->username, 
                $request->email, 
                $request->name, 
                $request->image_url
            );
            return redirect('/account')->with('success', 'アカウント情報を正常に更新しました。');
        }
        catch(Exception $e){
            return response()->json(['status' => 'error', 'reason' => $e->getMessage()]);
        }
    }

    public function updatePassword(Request $request){
        try{
            $this->userService->updatePassword(
                $this->auth,
                $request->current_password, 
                $request->password, 
                $request->password_confirmation
            );
            return redirect('/account')->with('success', 'パスワードを正常に更新しました。');
        }
        catch(Exception $e){
            return redirect('/account')->with('error', $e->getMessage());
        }
    }
}
