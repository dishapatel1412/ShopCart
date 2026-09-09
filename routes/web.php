<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ProductInquiryController;
use App\Http\Controllers\Admin\ProductInquiryController as AdminInquiryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use Illuminate\Support\Facades\Route;

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/verify-otp', [AuthController::class, 'showOtpForm'])->name('otp.form');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify');

Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
// Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/2fa', [AuthController::class, 'show2faForm'])->name('2fa.form');
Route::post('/2fa', [AuthController::class, 'verify2fa'])->name('2fa.verify');
Route::get('/2fa/enable', [AuthController::class, 'showEnable2faForm'])->name('2fa.enable.form');
Route::post('/2fa/enable', [AuthController::class, 'enable2fa'])->name('2fa.enable');
Route::post('/2fa/disable', [AuthController::class, 'disable2fa'])->name('2fa.disable');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/auth/{provider}', [SocialAuthController::class, 'loginSocial'])->name('socialite.auth');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callbackSocial'])->name('socialite.callback');


Route::middleware(['check.auth', 'check.session'])->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
    Route::post('/cart/coupon/apply', [CartController::class, 'applyCouponAjax'])->name('cart.coupon.ajax');
    Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove/{id}', [WishlistController::class, 'remove'])->name('wishlist.remove');

    Route::get('/panel', [PanelController::class , 'show'])->name('panel.show');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/contact', [ContactController::class, 'index'])->name('contact.form');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::get('/admin/contact', [AdminContactController::class, 'index'])->name('admin.contact.index');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
    Route::get('/order/success/{id}', [CheckoutController::class, 'orderSuccess'])->name('order.success');

    Route::get('/orders', [OrderController::class,'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/invoice', [OrderController::class, 'downloadinvoice'])->name('orders.invoice.download');

    Route::get('/inquiry', [ProductInquiryController::class, 'index'])->name('inquiry.index');
    Route::post('/inquiry', [ProductInquiryController::class, 'store'])->name('inquiry.store');
    Route::post('/admin/inquiry/{id}/reply', [AdminInquiryController::class, 'reply'])->name('admin.inquiry.reply');
    Route::get('/admin/inquiries', [AdminInquiryController::class, 'index'])->name('admin.inquiries');

    Route::get('/admin/orders', [AdminOrderController::class, 'index'])->name('admin.orders');
    Route::post('/admin/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::get('/admin/{id}/invoice', [AdminOrderController::class, 'downloadInvoice'])->name('admin.invoice.download');

    Route::post('/chat/upload', [ChatController::class, 'upload'])->name('chat.upload');
});

Route::get('/payment/success', [CheckoutController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/cancel',  [CheckoutController::class, 'paymentCancel'])->name('payment.cancel');
Route::post('/payment/webhook', [CheckoutController::class, 'paymentWebhook'])->name('payment.webhook');

Route::prefix('admin')->middleware(['admin', 'check.session'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
    Route::post('/products/{id}', [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::post('/products/{id}/delete', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::post('/import/excel', [AdminProductController::class, 'import'])->name('import.excel');
    Route::get('/export/excel', [AdminProductController::class, 'export'])->name('export.excel');

    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::post('/categories/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::post('/categories/{id}/delete', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    Route::get('/sizes', [SizeController::class, 'index'])->name('admin.sizes.index');
    Route::get('/sizes/create', [SizeController::class, 'create'])->name('admin.sizes.create');
    Route::post('/sizes', [SizeController::class, 'store'])->name('admin.sizes.store');
    Route::get('/sizes/{id}/edit', [SizeController::class, 'edit'])->name('admin.sizes.edit');
    Route::post('/sizes/{id}', [SizeController::class, 'update'])->name('admin.sizes.update');
    Route::post('/sizes/{id}/delete', [SizeController::class, 'destroy'])->name('admin.sizes.destroy');

    Route::get('/colors', [ColorController::class, 'index'])->name('admin.colors.index');
    Route::get('/colors/create', [ColorController::class, 'create'])->name('admin.colors.create');
    Route::post('/colors', [ColorController::class, 'store'])->name('admin.colors.store');
    Route::get('/colors/{id}/edit', [ColorController::class, 'edit'])->name('admin.colors.edit');
    Route::post('/colors/{id}', [ColorController::class, 'update'])->name('admin.colors.update');
    Route::post('/colors/{id}/delete', [ColorController::class, 'destroy'])->name('admin.colors.destroy');

    Route::get('/coupons', [CouponController::class, 'index'])->name('admin.coupons.index');
    Route::get('/coupons/create', [CouponController::class, 'create'])->name('admin.coupons.create');
    Route::post('/coupons', [CouponController::class, 'store'])->name('admin.coupons.store');
    Route::get('/coupons/{id}/edit', [CouponController::class, 'edit'])->name('admin.coupons.edit');
    Route::post('/coupons/{id}', [CouponController::class, 'update'])->name('admin.coupons.update');
    Route::post('/coupons/{id}/delete', [CouponController::class, 'destroy'])->name('admin.coupons.destroy');

    Route::get('/chat', [AdminChatController::class, 'index'])->name('admin.reply.chat');
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');