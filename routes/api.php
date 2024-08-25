<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\Api\EcomBackend\ImageAnalyzerController;

Route::get('/v1/getToken', [AuthController::class,'getAuthToken']);
Route::middleware('auth.jwt')->group(function() {
    Route::post('/v1/ecomBackend/put/analyzerRequest',[ImageAnalyzerController::class,'storeImageAnalyzer']);
});
