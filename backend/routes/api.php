<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\ClothingSuggestionsController;
use App\Http\Controllers\PhotoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\QuestionController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->put('user', [AuthController::class, 'update']);

Route::get('/weather', [WeatherController::class, 'getWeather']);

Route::post('/clothing-suggestions', [ClothingSuggestionsController::class, 'getClothingSuggestions']);

Route::post('/upload-photo', [App\Http\Controllers\PhotoController::class, 'uploadPhoto']);

Route::apiResource('questions', QuestionController::class);
