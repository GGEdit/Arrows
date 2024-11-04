<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FriendService;
use Exception;

class FriendController extends Controller
{
    private $auth;
    private $friendService;

    public function __construct(FriendService $friendService){
        $this->middleware(function ($request, $next){
            $this->auth = \Auth::user();
            return $next($request);
        });
        $this->friendService = $friendService;
    }

    public function index(){
        return view('/friend/index');
    }

    public function search(Request $request){
        try{
            $response = $this->friendService->search(
                $this->auth,
                $request->username
            );
            $user = $response['user'];
            $isFriend = $response['isFriend'];
            return view('/friend/add_me', compact('user', 'isFriend'));
        }
        catch(Exception $e){
            $errMessage = $e->getMessage();
            return view('/friend/index', compact('errMessage'));
        }
    }

    public function addMe($username){
        try{
            $response = $this->friendService->addMe(
                $this->auth,
                $username
            );
            $user = $response['user'];
            $isFriend = $response['isFriend'];
            return view('/friend/add_me', compact('user', 'isFriend'));
        }
        catch(Exception $e){
            $errMessage = $e->getMessage();
            return view('/friend/add_me', compact('errMessage'));
        }
    }

    public function store(Request $request){
        try{
            $this->friendService->store(
                $this->auth,
                $request->id
            );
            return redirect()->back()->with('success', '友だちを追加しました');
        }
        catch(Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
