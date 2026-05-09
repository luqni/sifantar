<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('page', 'index');
});

// Authentication Routes
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (Admin Only)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/articles', [AdminController::class, 'articles'])->name('admin.articles');
    Route::get('/admin/chat', [AdminController::class, 'chat'])->name('admin.chat');
    Route::get('/admin/delivery/{id}', [AdminController::class, 'showDelivery'])->name('admin.delivery.show');
    Route::post('/admin/delivery/{id}/status', [AdminController::class, 'updateDeliveryStatus'])->name('admin.delivery.status');
});

// Courier Routes (Courier Only)
Route::middleware(['auth', 'courier'])->group(function () {
    Route::get('/courier/dashboard', [CourierController::class, 'dashboard'])->name('courier.dashboard');
    Route::post('/courier/job/{id}/accept', [CourierController::class, 'acceptJob'])->name('courier.job.accept');
    Route::post('/courier/job/{id}/complete', [CourierController::class, 'completeJob'])->name('courier.job.complete');
    Route::post('/courier/location', [CourierController::class, 'updateLocation'])->name('courier.location.update');
});

// Patient/General Auth Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // Chat Routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');

    // Delivery Request Routes
    Route::get('/delivery-request', [\App\Http\Controllers\DeliveryRequestController::class, 'create'])->name('delivery-request.create');
    Route::post('/delivery-request', [\App\Http\Controllers\DeliveryRequestController::class, 'store'])->name('delivery-request.store');

    Route::get('/tracking/{id}', [HomeController::class, 'tracking'])->name('tracking.show');
    Route::get('/api/tracking/{id}', [HomeController::class, 'trackingApi'])->name('tracking.api');
    Route::get('/history', [HomeController::class, 'history'])->name('history');
    Route::get('/delivery/{id}', [HomeController::class, 'show'])->name('delivery.show');
});

// Page routes
Route::get('/{page}', function ($page) {
    if ($page === 'index' || $page === 'register') {
        if (auth()->check()) {
            if (auth()->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('page', 'home');
        }
    } else {
        // Protect other routes
        if (!auth()->check()) {
            return redirect()->route('page', 'index');
        }

        // Exclude pages that need specific controller logic
        if (in_array($page, ['tracking', 'delivery-request'])) {
            return redirect()->route('home');
        }
        
        // If admin tries to access patient home, redirect to admin dashboard
        if ($page === 'home' && auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
    }

    if (view()->exists("pages.{$page}")) {
        return view("pages.{$page}");
    }
    abort(404);
})->name('page');
