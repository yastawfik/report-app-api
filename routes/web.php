<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});
Route::get('/reports/{id}/download', [ReportController::class, 'download']);
Route::put('/reports/{id}', [ReportController::class, 'update']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
