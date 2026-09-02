<?php

use App\Http\Controllers\SpeakingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get("/speaking/questions", [SpeakingController::class, "questions"]);