<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportPdfController;

Route::apiResource('reports', ReportController::class);
Route::get('/reports', [ReportController::class, 'index']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);

Route::middleware('auth:sanctum')->post('/reports', [ReportController::class, 'store']);

Route::get('/reports/{id}/download', [ReportPdfController::class, 'download']);
Route::put('/reports/{id}', [ReportController::class, 'update']);

