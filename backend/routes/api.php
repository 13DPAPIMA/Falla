<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\UserController;
Route::middleware('auth:sanctum')->put('user', [UserController::class, 'update']);
Route::middleware('auth:sanctum')->post('verify-password', [UserController::class, 'verifyPassword']);

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

require __DIR__.'/auth.php';
