<?php

use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("speaking");
});

Route::get("/speaking", function () {
    return view("speaking");
});
