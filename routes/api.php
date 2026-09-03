<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpeakingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get("/speaking/questions", [SpeakingController::class, "questions"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post("/speaking/submit", [SpeakingController::class, "submit"]);
});

Route::get("/speaking/test-gemini", [SpeakingController::class, "testGemini"]);

Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post("/logout", [AuthController::class, "logout"]);
});
