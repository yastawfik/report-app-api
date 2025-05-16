<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/reports/all', [ReportController::class, 'allReports']);
Route::apiResource('reports', ReportController::class);

Route::get('/reports', [ReportController::class, 'index']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);

Route::middleware('auth:sanctum')->post('/reports', [ReportController::class, 'store']);

Route::get('/reports/{id}/download', [ReportController::class, 'download']);
Route::put('/reports/{id}', [ReportController::class, 'update']);


Route::get('/reports/{id}/generate-pdf', [ReportController::class, 'download']);


Route::put('/subreports/{id}', [ReportController::class, 'updateSubreport']);
Route::delete('/reports/{id}', [ReportController::class, 'destroy']);


