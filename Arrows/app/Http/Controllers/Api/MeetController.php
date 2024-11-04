<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MeetService;
use Exception;

class MeetController extends Controller
{
    private $meetService;

    public function __construct(MeetService $meetService){
        $this->meetService = $meetService;
    }

    public function notifyTerminateConference(Request $request){
        $authUser = $request->user();
        try{
            $this->meetService->notifyTerminateConference(
                $authUser->id, 
                $request->room_id, 
                $request->meet_name
            );
            return response()->json(['status' => 'success']);
        }
        catch(Exception $e){
            return response()->json(['status' => 'error', 'reason' => $e->getMessage()]);
        }
    }
}