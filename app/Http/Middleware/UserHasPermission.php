<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $currentGuard = $this->getCurrentGuard();

        if (empty($currentGuard) || ! Auth::guard($currentGuard)->user()->hasPermissionTo($permission)) {
            
            abort(403, 'You are unauthorized for this action.');

        }

        return $next($request);
    }

    protected function getCurrentGuard()
    {
        $currentGuard = NULL;

        if (Auth::guard('admin')->check()) {
            $currentGuard = 'admin';
        }
        else if (Auth::guard('manager')->check()) {
            $currentGuard = 'manager';
        }
        else if (Auth::guard('warehouse')->check()) {
            $currentGuard = 'warehouse';
        }
        else if (Auth::guard('owner')->check()) {
            $currentGuard = 'owner';
        }
        else if (Auth::guard('merchant')->check()) {
            $currentGuard = 'merchant';
        }

        return $currentGuard;
    }
}
