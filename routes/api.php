<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Profile\CreateProfile;
use App\Http\Controllers\Profile\ShowProfile;
use App\Http\Controllers\Profile\UpdateProfile;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/auth/register', RegisterController::class);
Route::post('/auth/login',    LoginController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', LogoutController::class);
    Route::get('/auth/me', fn(Request $r) => new UserResource($r->user()->load('profile')));

    Route::get('/profile',  ShowProfile::class);
    Route::post('/profile', CreateProfile::class);
    Route::put('/profile',  UpdateProfile::class);

    // resto das rotas autenticadas
});
