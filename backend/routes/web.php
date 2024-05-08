<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('/api/user/new', [UserController::class, 'createNewUser']);
Route::post('/api/user/login', [UserController::class, 'login']);
Route::get('/api/products', [ProductController::class, 'getAllProducts']);
Route::get('/api/product/{id}', [ProductController::class, 'getProductData']);
Route::post('/api/product/', [ProductController::class, 'createProduct']);
Route::put('/api/product/{id}', [ProductController::class, 'editProduct']);
Route::delete('/api/product/{id}', [ProductController::class, 'deleteProduct']);
