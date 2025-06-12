<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use App\Utility\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class authController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username" => "required|string",
            "password" => "required|string",
        ]);

        if ($validator->fails()) {
            return ApiResponse::send(422, "Validation failed", $validator->errors());
        }

        $credentials = $validator->validated();

        if (Auth::attempt($credentials)) {
            $user = User::where("username", $credentials["username"])->first();
            $user->update([
                "last_login_at" => now()
            ]);
            $token = $user->createToken("auth_token")->plainTextToken;
            return ApiResponse::send(200, "Logged in as user", null, [
                "token" => $token
            ]);
        }

        $admin = Admin::where("username", $credentials["username"])->first();

        if ($admin && Hash::check($credentials["password"], $admin->password)) {
            $admin->update([
                "last_login_at" => now()
            ]);
            $token = $admin->createToken("auth_token")->plainTextToken;
            return ApiResponse::send(200, "Logged in as admin", null, [
                "token" => $token
            ]);
        }

        return ApiResponse::send(400, "Wrong username or password");
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username" => "required|string|unique:admins,username",
            "password" => "required|string",
        ]);

        if ($validator->fails()) {
            return ApiResponse::send(422, "Validation failed", $validator->errors());
        }

        $credentials = $validator->validated();
        $credentials["password"] = Hash::make($credentials["password"]);

        $newAdmin = Admin::query()->create($credentials);
        Auth::attempt($credentials);

        $token = $newAdmin->createToken("auth_token")->plainTextToken;

        $newAdmin->update([
            "last_login_at" => Carbon::now()
        ]);

        return ApiResponse::send(200, "Registered", null, $token);
    }

    public function logout(Request $request)
    {
        Auth::guard("sanctum")->user()->tokens()->delete();
        return ApiResponse::send(200, "Logged out");
    }
}
