<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/welcome', [AuthController::class, 'welcome']);

Route::middleware('auth:sanctum')->group(function () {
    // Logout and authenticated user routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Update profile and change password routes
    Route::put('/user/update', [AuthController::class, 'updateProfile']);
    Route::put('/user/change-password', [AuthController::class, 'changePassword']);

    // Todo routes
    Route::get('/todos', [TodoController::class, 'index']);  // Get all todos
    Route::post('/todos', [TodoController::class, 'store']);  // Create a new todo
    Route::put('/todos/{id}', [TodoController::class, 'update']);  // Update a specific todo
    Route::delete('/todos/{id}', [TodoController::class, 'destroy']);  // Delete a specific todo

    // Department and designation data for dropdown (this is assuming it's for employee data)
    Route::get('/department-designation-data', [EmployeeController::class, 'fetchDropdownData']);
});

