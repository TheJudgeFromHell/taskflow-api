<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;

// Публичные маршруты (не требуют токен)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Защищённые маршруты (требуют токен)
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // CRUD задач
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    
    // Комментарии
    Route::post('/tasks/{id}/comments', [TaskController::class, 'addComment']);
    Route::get('/tasks/{id}/comments', [TaskController::class, 'getComments']);
    
    // Лайки/дизлайки
    Route::post('/tasks/{id}/rate', [TaskController::class, 'rateTask']);
});