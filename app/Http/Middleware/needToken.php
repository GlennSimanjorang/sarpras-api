<?php

namespace App\Http\Middleware;

use App\Utility\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class needToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->bearerToken()) {
            return ApiResponse::send(403, "Token not found");
        }

        if (!Auth::guard("sanctum")->check()) {
            return ApiResponse::send(403, "Invalid token");
        }

        return $next($request);
    }
}
