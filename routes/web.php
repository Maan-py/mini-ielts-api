<?php

use Illuminate\Support\Facades\Route;

Route::get("/speaking", function () {
    return view("speaking");
});
