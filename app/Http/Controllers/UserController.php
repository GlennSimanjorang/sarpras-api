<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utility\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ItemNotFoundException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return ApiResponse::send(200, "User list retrieved", null, $users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username" => "required|unique:users,username|min:4|max:60",
            "password" => "required|min:5|max:20"
        ]);

        if ($validator->fails()) {
            return ApiResponse::send(422, "Validator failed", $validator->errors()->all());
        }

        $cred = $validator->validated();
        $cred["password"] = Hash::make($cred["password"]);

        $newUser = User::query()->create($cred);

        return ApiResponse::send(200, "User created", null, $newUser);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $user = User::query()->find($id);
        if (is_null($user)) {
            return ApiResponse::send(200, "User not found");
        }
        return ApiResponse::send(200, "User retrieved", null, $user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return ApiResponse::send(404, "User not found");
        }

        $validator = Validator::make($request->all(), [
            "username" => "sometimes|min:4|max:60",
            "password" => "sometimes|min:5|max:10"
        ]);

        if ($validator->fails()) {
            return ApiResponse::send(422, "Validation failed", $validator->errors()->all());
        }

        $cred = $validator->validated();

        if (!empty($cred["password"])) {
            $cred["password"] = Hash::make($cred["password"]);
        }

        $user->update($cred);

        return ApiResponse::send(200, "User updated", null, $user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $user = User::destroy($id);

        if (!$user) {
            return ApiResponse::send(404, "User not found");
        }

        return ApiResponse::send(200, "User removed");
    }
}
