<?php

namespace App\Http\Middleware;

use App\Services\DeviceLimitService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LogoutDevice
{
    protected $deviceService;

    public function __construct(DeviceLimitService $deviceService)
    {
        $this->deviceService = $deviceService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isLogoutRequest($request)) {
            $deviceId = Session::get('device_id');

            if ($deviceId) {
                $this->deviceService->logoutDevice($deviceId);
            }
        }

        return $next($request);
    }

    private function isLogoutRequest(Request $request): bool
    {
        // check if this request is logout request from Laravel Fortify
        return $request->is('logout') || Route::currentRouteName() === 'logout';
    }
}
