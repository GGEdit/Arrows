<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;

class HomeController extends Controller
{
    private $auth;

    public function __construct(){
        $this->middleware(function ($request, $next){
            $this->auth = \Auth::user();
            return $next($request);
        });
    }

    public function index(){
        return view('/index');
    }
}