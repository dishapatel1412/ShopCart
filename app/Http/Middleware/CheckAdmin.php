<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role != 'admin') {
            return redirect('/dashboard')->with('error', 'Access Denied!');
        }
        return $next($request);
    }
}
