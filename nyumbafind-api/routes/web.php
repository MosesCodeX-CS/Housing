<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});
Route::get('/estates/{slug}', function (string $slug) {
    return view('estate-show', ['slug' => $slug]);
});

Route::get('/login', function () {
    return view('login');
});
