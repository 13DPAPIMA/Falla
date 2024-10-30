<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// user routes
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->put('user', [AuthController::class, 'update']);
Route::middleware('auth:sanctum')->post('verify-password', [AuthController::class, 'verifyPassword']);

// Show verification notice
Route::get('/email/verify', function () {
    return response()->json(['message' => 'Verify your email address.']);
})->middleware('auth:sanctum')->name('verification.notice');

// Handle email verification link click
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return response()->json(['message' => 'Email verified!']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

// Resend verification email
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification link sent!']);
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

// weather and suggestion routes
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\ClothingSuggestionsController;

Route::get('/weather', [WeatherController::class, 'getWeather']);

Route::post('/clothing-suggestions', [ClothingSuggestionsController::class, 'getClothingSuggestions']);

Route::post('/upload-photo', [App\Http\Controllers\PhotoController::class, 'uploadPhoto']);

// wardrobe routes
use App\Http\Controllers\ClothingController;
use App\Http\Controllers\WardrobeController;

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::get('/wardrobe', [WardrobeController::class, 'index']);
    Route::get('/available-clothing', [WardrobeController::class, 'availableClothing']);
    Route::post('/wardrobe', [WardrobeController::class, 'addToWardrobe']);
    Route::delete('/wardrobe/{id}', [WardrobeController::class, 'removeFromWardrobe']);

    Route::get('/clothing', [ClothingController::class, 'index']);
    Route::get('/clothing/{id}', [ClothingController::class, 'show']);
});

use App\Http\Controllers\QuestionController;

Route::resource('questions', QuestionController::class)->middleware(['auth:sanctum', 'verified']);

use App\Http\Controllers\AnswerController;

Route::resource('answers', AnswerController::class)->middleware(['auth:sanctum', 'verified']);

