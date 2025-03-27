<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to Laravel API CSV Importer',
    ]);
});

Route::post('upload', [ImportController::class, 'upload'])->name('upload');
Route::get('import-status/{id}', [ImportController::class, 'status'])->name('import-status');
Route::get('users', [UserController::class, 'index'])->name('users');
