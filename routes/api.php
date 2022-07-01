<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//unprotected route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//unprotected route
Route::post('/create-user', [\App\Http\Controllers\AuthController::class,'register']);
Route::post('/login', [\App\Http\Controllers\AuthController::class,'login']);
Route::post('/biometric-login', [\App\Http\Controllers\AuthController::class,'loginBiometric']);
Route::post('/is-field-available', [\App\Http\Controllers\AuthController::class,'isFieldAvailable']);
Route::post('/is-wallet-linked', [\App\Http\Controllers\AuthController::class,'isWalletLinked']);

//protected route
Route::group(['middleware'=>['auth:sanctum']], function (){
    Route::post('/logout', [\App\Http\Controllers\AuthController::class,'logout']);
    Route::post('/reset-password', [\App\Http\Controllers\AuthController::class,'resetPassword']);
    Route::post('/send-otp-code', [\App\Http\Controllers\AuthController::class,'sendOTP']);
    Route::post('/get-user', [\App\Http\Controllers\AuthController::class,'getUser']);
    Route::post('/verify-otp', [\App\Http\Controllers\AuthController::class,'verifyOTP']);
    Route::post('/update-basic-details', [\App\Http\Controllers\AuthController::class,'updateBasicUserProfile']); // kyc level 1

});



