<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->group(function () {
    // Show login form
    Route::get('login', [AdminController::class, 'create'])->name('admin.login');
    // Handle login form submission
    Route::post('login', [AdminController::class, 'store'])->name('admin.login.request');
    Route::group(['middleware' => ['admin']], function () {
        // Dashboard Route
        Route::get('dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        // Update Password page
        Route::get('update-password', [AdminController::class, 'edit'])->name('admin.update-password');
        // Verify Password Route
        Route::post('verify-password', [AdminController::class, 'verifyPassword'])->name('admin.verify.password');
        // Update Password Route
        Route::post('admin/update-password', [AdminController::class, 'updatePasswordRequest'])->name('admin.update-password.request');
        Route::get('logout', [AdminController::class, 'destroy'])->name('admin.logout');
    });
});
require __DIR__.'/auth.php';
