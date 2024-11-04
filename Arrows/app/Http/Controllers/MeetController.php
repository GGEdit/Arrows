<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MeetService;
use Exception;

class MeetController extends Controller
{
    private $auth;
    private $meetService;

    public function __construct(MeetService $meetService){
        $this->middleware(function ($request, $next){
            $this->auth = \Auth::user();
            return $next($request);
        });
        $this->meetService = $meetService;
    }

    public function index(){
        return view('/meet/index');
    }

    public function notifyConference(Request $request){
        try{
            $this->meetService->notifyConference(
                $this->auth->id, 
                $request->room_id, 
                $request->meet_name
            );
            return response()->json(['message' => 'success']);
        }
        catch(Exception $e){
            return response()->json(['status' => 'error', 'reason' => $e->getMessage()]);
        }
    }

    public function notifyTerminateConference(Request $request){
        try{
            $this->meetService->notifyTerminateConference(
                $this->auth->id, 
                $request->room_id, 
                $request->meet_name
            );
            return response()->json(['message' => 'success']);
        }
        catch(Exception $e){
            return response()->json(['status' => 'error', 'reason' => $e->getMessage()]);
        }
    }
}