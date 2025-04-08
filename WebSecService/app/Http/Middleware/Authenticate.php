<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    protected function redirectTo(Request $request)
    {
        if (!$request -> expectsJson()){
            return route ('login');
        }
    }
}
