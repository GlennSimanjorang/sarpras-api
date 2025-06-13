<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix("auth")->group(function () {
   Route::post("login", [\App\Http\Controllers\authController::class, "login"]);
   Route::post("register", [\App\Http\Controllers\authController::class, "register"]);
   Route::get("logout", [\App\Http\Controllers\authController::class, "logout"])->middleware("need-token");
});



Route::middleware("need-token")->group(function () {

    Route::prefix("admin")->group(function () {
        Route::apiResource("users", \App\Http\Controllers\UserController::class);
        Route::apiResource("categories", \App\Http\Controllers\CategoryController::class);

        Route::apiResource("items", \App\Http\Controllers\ItemController::class);
        Route::post("items/{sku}/change-image", [\App\Http\Controllers\ItemController::class, "changeImage"]);

        Route::apiResource("borrows", \App\Http\Controllers\BorrowingController::class)->only(["index","show"]);
        Route::apiResource("returns", \App\Http\Controllers\ReturningController::class)->only(["index","show"]);

        Route::patch("borrows/{id}/approve", [\App\Http\Controllers\BorrowingController::class, "approve"]);
        Route::patch("borrows/{id}/reject", [\App\Http\Controllers\BorrowingController::class, "reject"]);

        Route::patch("returns/{id}/approve", [\App\Http\Controllers\ReturningController::class, "approve"]);
        Route::patch("returns/{id}/reject", [\App\Http\Controllers\ReturningController::class, "reject"]);
        Route::apiResource("dashboard/count", \App\Http\Controllers\DashboardController::class)->only(["index"]);
        Route::apiResource("/export/borrowings", \App\Http\Controllers\ExportDataController::class)->only(["index"]);
        Route::apiResource("/export/returning", \App\Http\Controllers\ExportReturningController::class)->only(["index"]);
    })->middleware("admin-only");

    Route::prefix("user")->group(function () {
        Route::get("borrow-history", [\App\Http\Controllers\BorrowingController::class, "userBorrowHistory"]);
        Route::post("borrow-request", [\App\Http\Controllers\BorrowingController::class, "borrowRequest"]);
        Route::apiResource("items", \App\Http\Controllers\ItemController::class);
        Route::get("return-history", [\App\Http\Controllers\ReturningController::class, "userReturnHistory"]);
        Route::post("return-request", [\App\Http\Controllers\ReturningController::class, "returnRequest"]);
    });
});


Route::fallback(function () {
    return \App\Utility\ApiResponse::send(404, "Route not found");
});
