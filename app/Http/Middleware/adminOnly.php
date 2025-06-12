<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Utility\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class adminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentUser = Auth::guard("sanctum")->user();
        $admin = Admin::query()->where("id", $currentUser->id)->where("username", $currentUser->username)->first();

        if (!$admin) {
            return ApiResponse::send(403, "You're not admin");
        }
        return $next($request);
    }
}
