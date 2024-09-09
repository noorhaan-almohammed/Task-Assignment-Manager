<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes accessible without authentication
Route::post('/login', [AuthController::class, 'login']);
Route::get('/tasks', [TaskController::class, 'index']);

// Routes for authenticated users
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);

    // Routes for users with the 'employee' role
    Route::middleware('role:employee')->group(function () {
        // Add any employee-specific routes here
    });

    // Routes for users with the 'manager' role
    Route::middleware('role:manager')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);

        Route::post('/tasks', [TaskController::class, 'store']);
        Route::put('/tasks/{task}/assign', [TaskController::class, 'assignTask']);
        Route::put('/tasks/{task}/unAssign', [TaskController::class, 'unAssignTask']);
        Route::put('/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    });

    // Routes for users with the 'admin' role
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        Route::post('/tasks', [TaskController::class, 'store']);
        Route::put('/tasks/{task}/assign', [TaskController::class, 'assignTask']);
        Route::put('/tasks/{task}/unAssign', [TaskController::class, 'unAssignTask']);
        Route::put('/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    });
});
