<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\View\Factory;
use Illuminate\Http\Request;

class SetAppInfo
{
    private $viewFactory;

    public function __construct(Factory $viewFactory){
        $this->viewFactory = $viewFactory;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $appVersion = config('app.app_version');
        $s3Url = config('app.s3_url');
        $nsocketServer = config('app.nsocket_server');
        $meetDomain = config('app.meet_domain');
        $meetServer = config('app.meet_server');
        $appInfos = [
            'app_version' => $appVersion,
            's3_url' => $s3Url,
            'nsocket_server' => $nsocketServer,
            'meet_domain' => $meetDomain,
            'meet_server' => $meetServer,
        ];
        $this->viewFactory->share('app_info', $appInfos);
        return $next($request);
    }
}
