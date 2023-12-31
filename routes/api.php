<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\OrderController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'middleware' => 'api',
    'prefix' => 'v1'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/change-pass', [AuthController::class, 'changePassWord']);

    Route::post('/add-to-cart', [CartController::class, 'addProductToCart']);
    Route::post('/show-cart', [CartController::class, 'showCart']);

    Route::post('/show-order', [OrderController::class, 'listOrderById']);
    Route::post('/show-all-order', [OrderController::class, 'showOrders']);

    Route::post('/show-history-payments', [PaypalController::class, 'listHistoryPayments']);

    Route::post('handle-payment', [PaypalController::class, 'handlePayment'])->name('make.payment');
});

Route::view('payment', 'paypal.index')->name('create.payment');
Route::get('cancel-payment', [PaypalController::class, 'paymentCancel'])->name('cancel.payment');
Route::get('payment-success', [PaypalController::class, 'paymentSuccess'])->name('success.payment');
