<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/welcome', [AuthController::class, 'welcome']);

// Logout and authenticated user routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('user')->group(function () {
        
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/', [AuthController::class, 'user']);
    // Update profile and change password routes
    Route::put('/update', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    });
    // Todo routes
    Route::prefix('todos')->group(function () {
    Route::get('/', [TodoController::class, 'index']);  
    Route::post('/', [TodoController::class, 'store']);  
    Route::put('/{id}', [TodoController::class, 'update']);  
    Route::delete('/{id}', [TodoController::class, 'destroy']);  
 });
    // Department and designation data for dropdown (this is assuming it's for employee data)
    Route::get('/department-designation-data', [EmployeeController::class, 'fetchDropdownData']);
});

