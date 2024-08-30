<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\Api\EcomBackend\ImageAnalyzerController;

Route::get('/v1/getToken', [AuthController::class,'getAuthToken']);
Route::middleware('auth.jwt')->group(function() {
    Route::post('/v1/ecomBackend/put/analyzerRequest',[ImageAnalyzerController::class,'storeImageAnalyzer']);
    Route::post('/v1/ecomBackend/get/analyzedResponse',[ImageAnalyzerController::class,'analyzedResponse']);
    Route::post('/v1/ecomBackend/put/storeAnalyzedResponse',[ImageAnalyzerController::class,'StoreAnalyzedResponse']);
    Route::post('/v1/ecomBackend/put/updateAnalyzeData',[ImageAnalyzerController::class,'UpdateAnalyzeData']);
    Route::post('/v1/ecomBackend/get/getResponseHistory',[ImageAnalyzerController::class,'getResponseHistory']);
    Route::post('/v1/ecomBackend/get/getEcomProducts',[ImageAnalyzerController::class,'getEcomProducts']);
    Route::post('/v1/ecomBackend/get/getUserRequests',[ImageAnalyzerController::class,'getUserRequests']);
});
