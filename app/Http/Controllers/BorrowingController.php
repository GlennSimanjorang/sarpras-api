<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Borrowing;
use App\Models\Item;
use App\Utility\ApiResponse;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $borrowings = Borrowing::with(["user","item"])->get();
        return ApiResponse::send(200, "Borrowing records retrieved", null, $borrowings);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $borrowing = Borrowing::query()->with(["user", "item"])->find($id);

        if (is_null($borrowing)) {
            return ApiResponse::send(404, "Borrowing record not found");
        }

        return ApiResponse::send(200, "Borrowing record found", null, $borrowing);
    }

    public function approve(Request $request, int $id)
    {
        $borrowing = Borrowing::with(["user", "item"])->find($id);

        if (!$borrowing) {
            return ApiResponse::send(404, "Borrowing record not found");
        }

        if ($borrowing->status !== "pending") {
            return ApiResponse::send(400, "Borrow record already processed");
        }

        $currentUser = Auth::guard("sanctum")->user();

        $borrowing->update([
            "status" => "approved",
            "approved_by" => $currentUser->id,
            "approved_at" => now()
        ]);



        return ApiResponse::send(200, "Borrowing approved", null, $borrowing);
    }

    public function reject(Request $request, int $id)
    {
        $borrowing = Borrowing::query()->with(["user","item"])->find($id);

        if ($borrowing->status != "pending") {
            return ApiResponse::send(400, "This borrow record already approved/rejected");
        }

        if (is_null($borrowing)) {
            return ApiResponse::send(404, "Borrowing record not found");
        }

        $borrowing->update([
            "status" => "rejected"
        ]);

        return ApiResponse::send(200, "Borrowing rejected", null, $borrowing);
    }

    public function borrowRequest(Request $request)
    {
        $currentUser = Auth::guard("sanctum")->user();
        $user = \App\Models\User::query()->where("id", $currentUser->id)->where("username", $currentUser->username)->first();

        if (is_null($user)) {
            return ApiResponse::send(404, "User not found");
        }


        $validator = Validator::make($request->all(), [
           "sku" => "required|exists:items,sku",
           "quantity" => "required|integer"
        ]);

        if ($validator->fails()) {
            return ApiResponse::send(422, "Validation failed", $validator->errors()->all());
        }

        $item = Item::query()->where("sku", $request->sku)->first();

        $previousBorrow = Borrowing::query()->where("user_id", $user->id)->where("item_id", $item->id)->where("status", "pending")->exists();
        if ($previousBorrow) {
            return ApiResponse::send(403, "Previous borrow request still pending, please wait until its not pending");
        }

        if ($item->stock - $request->quantity <= 0) {
            return ApiResponse::send(400, "Cannot borrow item with that quantity, item stock: " . $item->stock);
        }

        $newBorrowing = Borrowing::query()->create([
            "item_id" => $item->id,
            "quantity" => $request->quantity,
            "user_id" => $user->id,
            "status" => "pending"
        ]);
        $newBorrowing = Borrowing::query()->with("item")->find($newBorrowing->id);

        return ApiResponse::send(200, "Borrowing requested", null, $newBorrowing);
    }

    public function userBorrowHistory(Request $request)
    {
        $currentUser = Auth::guard("sanctum")->user();
        $user = \App\Models\User::query()->where("id", $currentUser->id)->where("username", $currentUser->username)->first();

        if (is_null($user)) {
            return ApiResponse::send(404, "User not found");
        }

        $borrowings = Borrowing::query()->with("item")->where("user_id", $user->id)->get();
        return ApiResponse::send(200, "User borrowing records retrieved", null, $borrowings);
    }
}
