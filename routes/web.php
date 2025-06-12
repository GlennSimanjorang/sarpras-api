<?php

use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    return \App\Utility\ApiResponse::send(404, "Route not found");
});
