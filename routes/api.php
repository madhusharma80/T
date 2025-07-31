<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/todos', [TodoController::class, 'store']);
    Route::get('/todos', [TodoController::class, 'index']);
    // Other protected routes



    // Employee routes
    Route::get('/department-designation-data', [EmployeeController::class, 'getDepartmentAndDesignationData']);
    Route::get('/employees', [EmployeeController::class, 'getEmployees']);
    Route::post('/employees', [EmployeeController::class, 'addEmployee']);
});
