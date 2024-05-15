<?php

use App\Http\Controllers\API\UserControlle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/Signup',[UserControlle::class,'signup'])->name('Signup');

Route::post('/Logout',[UserControlle::class,'logout'])->name('Logout')
->middleware('auth:sanctum');

Route::post('/VerifiedEmail',[UserControlle::class,'verifiedEmail'])->name('verifiedEmail');

Route::post('/Login',[UserControlle::class,'login'])->name('Login');

Route::post('/RefreshToken',[UserControlle::class,'refreshToken'])->name('RefreshToken')
->middleware('auth:sanctum');