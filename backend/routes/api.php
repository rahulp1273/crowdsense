<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\PlaceController as AdminPlaceController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\User\PlaceController as UserPlaceController;
use App\Http\Controllers\User\CheckInController as UserCheckInController;
use App\Http\Controllers\User\InquiryController as UserInquiryController;
use App\Http\Controllers\User\PredictionController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Admin\AdminManagementController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Public/User Data
Route::get('/places', [UserPlaceController::class, 'index']);
Route::get('/places/{id}/crowd', [UserPlaceController::class, 'showCrowd']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // User GPS & Inquiry Actions
    Route::post('/checkin', [UserCheckInController::class, 'store']);
    Route::post('/checkout', [UserCheckInController::class, 'destroy']);
    Route::get('/user/active-checkin', [UserCheckInController::class, 'active']);
    Route::get('/user/profile', [ProfileController::class, 'show']);
    Route::put('/user/profile', [ProfileController::class, 'update']);
    Route::post('/places/{id}/inquiries', [UserInquiryController::class, 'store']);
    Route::get('/user/inquiries', [UserInquiryController::class, 'index']);
    
    // Predictions
    Route::get('/places/{id}/prediction', [PredictionController::class, 'show']);

    // Admin Actions
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/admin/analytics', [AdminDashboardController::class, 'analytics']);
    Route::get('/admin/places', [AdminPlaceController::class, 'index']);
    Route::get('/admin/inquiries', [AdminInquiryController::class, 'index']);
    Route::patch('/admin/inquiries/{id}/seen', [AdminInquiryController::class, 'markSeen']);
    
    Route::get('/admin/lookup-pincode/{pincode}', [AdminPlaceController::class, 'lookupPincode']);
    Route::post('/admin/places', [AdminPlaceController::class, 'store']);
    Route::put('/admin/places/{id}', [AdminPlaceController::class, 'update']);
    Route::delete('/admin/places/{id}', [AdminPlaceController::class, 'destroy']);
    // Admin Management Routes
    Route::prefix('admin')->group(function () {
        Route::get('/admins', [AdminManagementController::class, 'index']);
        Route::post('/admins', [AdminManagementController::class, 'store']);
        Route::put('/admins/{id}', [AdminManagementController::class, 'update']);
        Route::delete('/admins/{id}', [AdminManagementController::class, 'destroy']);
    });
});
