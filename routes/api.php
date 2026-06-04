<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CaixasFinaceiras\CreateCaixaFinanceira;
use App\Http\Controllers\CaixasFinaceiras\ShowCaixaFinanceira;
use App\Http\Controllers\CaixasFinaceiras\UpdateCaixaFinanceira;
use App\Http\Controllers\fontes_renda\createFontesRenda;
use App\Http\Controllers\fontes_renda\ShowFontesRenda;
use App\Http\Controllers\fontes_renda\UpdateFontesRenda;
use App\Http\Controllers\Profile\CreateProfile;
use App\Http\Controllers\Profile\ShowProfile;
use App\Http\Controllers\Profile\UpdateProfile;
use App\Http\Controllers\Rendas\CreateRenda;
use App\Http\Controllers\Rendas\ShowRenda;
use App\Http\Controllers\Rendas\UpdateRenda;
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

    // Fontes de renda
    Route::get('/fontes-renda',       ShowFontesRenda::class);
    Route::post('/fontes-renda',      createFontesRenda::class);
    Route::put('/fontes-renda/{id}',  UpdateFontesRenda::class);

    // Rendas
    Route::get('/rendas',       ShowRenda::class);
    Route::post('/rendas',      CreateRenda::class);
    Route::put('/rendas/{id}',  UpdateRenda::class);

    // Caixas financeiras
    Route::get('/caixas-financeiras',       ShowCaixaFinanceira::class);
    Route::post('/caixas-financeiras',      CreateCaixaFinanceira::class);
    Route::put('/caixas-financeiras/{id}',  UpdateCaixaFinanceira::class);
});
