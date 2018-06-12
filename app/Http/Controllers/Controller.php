<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;


    public static function isClient()
    {
        return Auth::guard('client')->check();
    }

    public static function isAdmin()
    {
        return Auth::guard('company')->check() && Auth::guard('company')->id() == '1';
    }

    public static function isCompany()
    {
        return Auth::guard('company')->check() && Auth::guard('company')->id() != '1';
    }

    public static function isCompanyOrAdmin()
    {
        return Auth::guard('company')->check();
    }

    public static function isLoggedIn()
    {
        return Auth::guard('company')->check() || Auth::guard('client')->check();
    }
}
