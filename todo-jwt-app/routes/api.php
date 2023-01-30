<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\UserController; 
use App\Http\Controllers\api\SaleController; 
use App\Http\Controllers\api\PurchaseController; 


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


// Route::post('/register',[UserController::class, 'register']);
// Route::post('/login',[UserController::class, 'login']);
// route::get('/logout',[UserController::class, 'logout']);

Route::controller(UserController::class)->group(function(){
    Route::post('/register','register');
    Route::post('/login','login');
    Route::get('/logout','logout');
    Route::get('/refresh','refresh');   
});

Route::middleware('auth:api')->group(function () {
    route::apiResource('user',UserController::class);
    route::apiResource('product',ProductController::class);
    route::apiResource('sale',SaleController::class);
    route::apiResource('Purchase',PurchaseController::class);
});

