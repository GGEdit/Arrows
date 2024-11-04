<?php

namespace App\Http\Controllers;

use App\Services\RoomService;
use Exception;

class RoomController extends Controller
{
    private $auth;
    private $roomService;

    public function __construct(RoomService $roomService){
        $this->middleware(function ($request, $next){
            $this->auth = \Auth::user();
            return $next($request);
        });
        $this->roomService = $roomService;
    }

    public function getRoomList(){
        try{
            return $this->roomService->getRoomList(
                $this->auth->id
            );
        }
        catch(Exception $e){
            return response()->json(['status' => 'error', 'reason' => $e->getMessage()]);
        }
    }
}
