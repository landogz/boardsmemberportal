<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/example', function () {
    return view('example');
});

Route::post('/api/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Data received successfully',
        'data' => request()->all()
    ]);
});
