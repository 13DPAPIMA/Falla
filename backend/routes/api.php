<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// user routes
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->put('user', [AuthController::class, 'update']);


// weather and suggestion routes
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\ClothingSuggestionsController;

Route::get('/weather', [WeatherController::class, 'getWeather']);

Route::post('/clothing-suggestions', [ClothingSuggestionsController::class, 'getClothingSuggestions']);

Route::post('/upload-photo', [App\Http\Controllers\PhotoController::class, 'uploadPhoto']);


// wardrobe routes
use App\Http\Controllers\ClothingController;
use App\Http\Controllers\WardrobeController;

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/wardrobe', [WardrobeController::class, 'index']);
    Route::get('/available-clothing', [WardrobeController::class, 'availableClothing']);
    Route::post('/wardrobe', [WardrobeController::class, 'addToWardrobe']);
    Route::delete('/wardrobe/{id}', [WardrobeController::class, 'removeFromWardrobe']);

    Route::get('/clothing', [ClothingController::class, 'index']);
    Route::get('/clothing/{id}', [ClothingController::class, 'show']);
});
