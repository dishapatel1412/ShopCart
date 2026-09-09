<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ProductInquiryController;
use App\Http\Controllers\Api\ChatController;

use App\Http\Controllers\Api\Admin\ProductInquiryController as AdminProductInquiryController;
use App\Http\Controllers\Api\Admin\ChatController as AdminChatController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('products', [ProductController::class, 'index']);
Route::post('products', [ProductController::class, 'store']);
Route::put('products/{id}', [ProductController::class, 'update']);
Route::delete('products/{id}', [ProductController::class, 'destroy']);

Route::post('import', [ProductController::class, 'import']);
Route::get('export', [ProductController::class, 'export']);

Route::get('sizes', [SizeController::class, 'index']);
Route::post('sizes', [SizeController::class, 'store']);
Route::put('sizes/{id}', [SizeController::class, 'update']);
Route::delete('sizes/{id}', [SizeController::class, 'destroy']);

Route::get('colors', [ColorController::class, 'index']);
Route::post('colors', [ColorController::class, 'store']);
Route::put('colors/{id}', [ColorController::class, 'update']);
Route::delete('colors/{id}', [ColorController::class, 'destroy']);

Route::get('categories', [CategoryController::class, 'index']);
Route::post('categories', [CategoryController::class, 'store']);
Route::put('categories/{id}', [CategoryController::class, 'update']);
Route::delete('categories/{id}', [CategoryController::class, 'destroy']);

Route::get('contact', [ContactController::class, 'index']);
Route::post('contact', [ContactController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('cart', [CartController::class, 'store']);
    Route::get('cart', [CartController::class, 'index']);
    Route::put('cart/{id}', [CartController::class, 'update']);
    Route::delete('cart/{id}', [CartController::class, 'destroy']);

    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::put('orders/{id}', [OrderController::class, 'update']);
    Route::delete('orders/{id}', [OrderController::class, 'cancel']);

    Route::get('wishlist', [WishlistController::class, 'index']);
    Route::post('wishlist', [WishlistController::class, 'store']);
    Route::delete('wishlist/{id}', [WishlistController::class, 'destroy']);

    Route::post('checkout', [CheckoutController::class, 'placeOrder']);

    Route::get('inquiry', [ProductInquiryController::class, 'index']);
    Route::post('inquiry', [ProductInquiryController::class, 'store']);

    Route::get('/admin/inquiry', [AdminProductInquiryController::class, 'index']);
    Route::post('/admin/inquiry/{inquiry}/reply', [AdminProductInquiryController::class, 'store']);

    Route::get('chat', [ChatController::class, 'viewMessage']);
    Route::post('chat/message', [ChatController::class, 'sendMessage']);
    Route::post('chat/location', [ChatController::class, 'sendLocation']);
    Route::post('chat/status', [ChatController::class, 'updateStatus']);
    Route::get('chat/download/{file}', [ChatController::class, 'downloadPdf']);
    
    Route::get('admin/chat', [AdminChatController::class, 'adminViewAll']);
    Route::post('admin/chat/{userId}', [AdminChatController::class, 'viewConversation']);
    Route::post('admin/chat/{userId}/message', [AdminChatController::class, 'replyToUser']);
    Route::post('admin/chat/{userId}/location', [AdminChatController::class, 'adminSendLocation']);
});