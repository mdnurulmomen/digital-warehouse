<?php

namespace App\Http\Controllers;

// use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ApplicationSetting;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only('index');
        $this->middleware('auth:admin')->only('adminHome');
        $this->middleware('auth:manager')->only('managerHome');
        $this->middleware('auth:owner')->only('ownerHome');
        $this->middleware('auth:warehouse')->only('warehouseHome');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $access_token = Str::random(60);
        // $roles = Auth::user()->roles;
        // $permissions = Auth::user()->permissions;
        $generalSettings = ApplicationSetting::firstOrCreate([]);

        return view('layouts.merchant', [/*'permissions' => $permissions, 'roles' => $roles, 'access_token' => $access_token,*/ 'general_settings' => $generalSettings]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adminHome()
    {
        // $accessToken = Str::random(60);
        $roles = Auth::guard('admin')->user()->roles;
        $permissions = Auth::guard('admin')->user()->permissions;
        $generalSettings = ApplicationSetting::firstOrCreate([]);

        return view('layouts.admin', ['permissions' => $permissions, 'roles' => $roles, /*'access_token' => $accessToken,*/ 'general_settings' => $generalSettings]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function managerHome()
    {
        // $accessToken = Str::random(60);
        $roles = Auth::guard('manager')->user()->roles;
        $permissions = Auth::guard('manager')->user()->permissions;
        $generalSettings = ApplicationSetting::firstOrCreate([]);

        return view('layouts.manager', ['permissions' => $permissions, 'roles' => $roles, /*'access_token' => $accessToken,*/ 'general_settings' => $generalSettings]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function ownerHome()
    {
        // $accessToken = Str::random(60);
        // $roles = Auth::guard('owner')->user()->roles;
        // $permissions = Auth::guard('owner')->user()->permissions;
        $generalSettings = ApplicationSetting::firstOrCreate([]);

        return view('layouts.owner', [/*'permissions' => $permissions, 'roles' => $roles, 'access_token' => $accessToken,*/ 'general_settings' => $generalSettings]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function warehouseHome()
    {
        // $accessToken = Str::random(60);
        // $roles = Auth::guard('warehouse')->user()->roles;
        // $permissions = Auth::guard('warehouse')->user()->permissions;
        $generalSettings = ApplicationSetting::firstOrCreate([]);

        return view('layouts.warehouse', [/*'permissions' => $permissions, 'roles' => $roles, 'access_token' => $accessToken,*/ 'general_settings' => $generalSettings]);
    }
}
