<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\UserDevice;
use App\Services\DeviceLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDeviceLimit
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
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        if ($request->routeIs('login') || $request->routeIs('logout')) {
            return $next($request);
        }

        $sessionDeviceId = session('device_id');

        // check if session device_id still valid in database
        $device = UserDevice::where('user_id', $user->id)
            ->where('device_id', $sessionDeviceId)
            ->first();

        if (!$device) {
            // if not present, try to re-register the device
            $device = $this->deviceService->registerDevice($user);

            if (!$device) {
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors(['device' => 'Anda telah mencapai batas maksimum perangkat. Silakan logout dari perangkat lain.']);
            }
        }

        return $next($request);
    }
}
