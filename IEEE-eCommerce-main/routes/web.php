<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});


route::resource('product',ProductController::class);


/*
Route::get('user', [ProductController::class,'index']);
Route::get('user/create', [ProductController::class,'create']);
Route::post('user/store', [ProductController::class,'store']);
Route::get('user/show/{id}', [ProductController::class,'show']);
Route::get('user/edit/{id}', [ProductController::class,'edit']);
Route::put('user/update/{id}', [ProductController::class,'update']);
Route::delete('user/destroy/{id}', [ProductController::class,'destroy']);
*/
