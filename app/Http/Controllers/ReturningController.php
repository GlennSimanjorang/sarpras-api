<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\Returning;
use App\Utility\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReturningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
{
    // Tambahkan with untuk memuat relasi borrowing, borrowing.item, dan borrowing.user
    $returnings = Returning::with([
        'borrowing', 
        'borrowing.item', 
        'borrowing.user'
    ])->get();
    
    return ApiResponse::send(200, "Returning list retrieved", null, $returnings);
}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $returning = Returning::query()->with(["borrowing","admin"])->find($id);
        if (is_null($returning)) {
            return ApiResponse::send(404, "Returning record not found");
        }
        return ApiResponse::send(200, "Returning record found", null, $returning);
    }

    public function userReturnHistory(Request $request)
    {
        $currentUser = Auth::guard("sanctum")->user();
        $user = \App\Models\User::query()->where("id", $currentUser->id)->where("username", $currentUser->username)->first();

        if (is_null($user)) {
            return ApiResponse::send(404, "User not found");
        }

        $returnings = Returning::query()
            ->join("borrowings", "returnings.borrow_id", "=", "borrowings.id")
            ->with(["borrowing","borrowing.item"])
            ->where("borrowings.user_id", $user->id)
            ->get();
        return ApiResponse::send(200, "User returnings record retrieved", null, $returnings);

    }

    public function returnRequest(Request $request)
    {
        $currentUser = Auth::guard("sanctum")->user();
        $user = \App\Models\User::query()->where("id", $currentUser->id)->where("username", $currentUser->username)->first();

        if (is_null($user)) {
            return ApiResponse::send(404, "User not found");
        }

        $validator = Validator::make($request->all(), [
            "borrow_id" => "required|exists:borrowings,id",
            "returned_quantity" => "required|integer"
        ]);

        if ($validator->fails()) {
            return ApiResponse::send(422, "Validation failed", $validator->errors()->all());
        }

        $borrowing = Borrowing::query()->where("id", $request->borrow_id)->first();
        if (is_null($borrowing)) {
            return ApiResponse::send(404, "Borrowing record not found");
        }

        if ($borrowing->user_id != $user->id) {
            return ApiResponse::send(403, "This borrowing is not yours");
        }


        $previousReturn = Returning::join('borrowings', 'returnings.borrow_id', '=', 'borrowings.id')
            ->where('returnings.borrow_id', $request->borrow_id)
            ->where('borrowings.user_id', $user->id)
            ->where('borrowings.status', 'pending')
            ->exists();

        if ($previousReturn) {
            return ApiResponse::send(403, "Previous return request still pending, please wait until it's not pending");
        }


        $borrowing->update([
            "status" => "pending"
        ]);

        $newReturning = Returning::query()->create($validator->validated());
        $newReturning = Returning::query()->with("borrowing")->find($newReturning->id);
        return ApiResponse::send(200, "Returned, please wait for admin approval, check returning status regularly", null, $newReturning);
    }

    public function approve(Request $request, int $id)
    {
       $returning = Returning::query()->with(["borrowing"])->find($id);

        if (is_null($returning)) {
            return ApiResponse::send(404, "Returning record not found");
        }

        $currentUser = Auth::guard("sanctum")->user();
        $admin = \App\Models\Admin::query()->where("id", $currentUser->id)->where("username", $currentUser->username)->first();

        $borrowing = Borrowing::query()->find($returning->borrow_id);
        if (is_null($borrowing)) {
            return ApiResponse::send(404, "Borrowing record not found");
        }

        if ($borrowing->status != "pending") {
            return ApiResponse::send(400, "This borrow record already returned/rejected");
        }

        $item = Item::query()->find($returning->borrowing->item_id);

        $item->update([
            "stock" => $item->stock += $returning->borrowing->quantity
        ]);
        $borrowing->update([
            "status" => "returned"
        ]);

        $returning->update([
            "handled_by" => $admin->id
        ]);

        $item = $returning->borrowing->item;
        return ApiResponse::send(200, "Returned approved", null, $returning);
    }

    public function reject(Request $request, int $id)
    {
        $returning = Returning::query()->with("borrowing")->find($id);

        if (is_null($returning)) {
            return ApiResponse::send(404, "Returning record not found");
        }

        $borrowing = Borrowing::query()->find($returning->borrow_id);
        if (is_null($borrowing)) {
            return ApiResponse::send(404, "Borrowing record not found");
        }

        if ($borrowing->status != "pending") {
            return ApiResponse::send(400, "This borrow record already returned/rejected");
        }

        $currentUser = Auth::guard("sanctum")->user();
        $admin = \App\Models\Admin::query()->where("id", $currentUser->id)->where("username", $currentUser->username)->first();

        $borrowing->update([
            "status" => "rejected"
        ]);

        $returning->update([
            "handled_by" => $admin->id
        ]);

        return ApiResponse::send(200, "Returned rejected", null, $returning);
    }
}
