<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MessageService;
use Exception;

class MessageController extends Controller
{
    private $auth;
    private $messageService;

    public function __construct(MessageService $messageService){
        $this->middleware(function ($request, $next){
            $this->auth = \Auth::user();
            return $next($request);
        });
        $this->messageService = $messageService;
    }

    public function get($id){
        try{
            return $this->messageService->get(
                $id
            );
        }
        catch(Exception $e){
            return response()->json(['status' => 'error', 'reason' => $e->getMessage()]);
        }
    }

    public function store(Request $request){
        try{
            $this->messageService->store(
                $this->auth->id,
                $request->room_id,
                $request->file('attachment'),
                $request->content
            );
            return response()->json(['message' => 'success']);
        }
        catch(Exception $e){
            return response()->json(['status' => 'error', 'reason' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id){
        try{
            $this->messageService->update(
                $id,
                $this->auth->id,
                $request->content
            );
            return response()->json(['message' => 'success']);
        }
        catch(Exception $e){
            return response()->json(['status' => 'error', 'reason' => $e->getMessage()]);
        }
    }

    public function delete($id){
        try{
            $this->messageService->delete(
                $id,
                $this->auth->id,
            );
            return response()->json(['message' => 'success']);
        }
        catch(Exception $e){
            return response()->json(['status' => 'error', 'reason' => $e->getMessage()]);
        }
    }
}