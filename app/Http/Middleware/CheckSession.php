<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user()->fresh();

            if ($user->session_id !== Session::getId()) {
                Auth::logout();

                return redirect('/login')
                    ->with('message', 'Session expired. Please login again.');
            }
        }
        return $next($request);
    }
}
