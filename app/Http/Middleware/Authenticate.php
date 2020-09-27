<?php

namespace App\Http\Middleware;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {

            $routeName = Route::currentRouteName();

            if (Str::contains($routeName, 'admin.')) {
                return route('admin.login');
            }
            elseif (Str::contains($routeName, 'owner.')) {
                return route('owner.login');
            }
            elseif (Str::contains($routeName, 'warhouse.')) {
                return route('warhouse.login');
            }
            elseif (Str::contains($routeName, 'manager.')) {
                return route('manager.login');
            }
            else
                return route('login');
            
        }
    }
}
