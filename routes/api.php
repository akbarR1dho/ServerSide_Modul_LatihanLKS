<?php

use App\Http\Controllers\FormsController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\ResponsesController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix("/v1")->group(function () {
    // Auth
    Route::prefix("/auth")->group(function () {
        Route::post('/login', [UsersController::class, 'login']);
        Route::post('/logout', [UsersController::class, 'logout'])->middleware('jwt.middleware');
        Route::post('/register', [UsersController::class, 'register']);
    });

    // Form
    Route::prefix("/forms")->group(function () {
        Route::post('/', [FormsController::class, 'post'])->middleware('jwt.middleware');
        Route::get('/', [FormsController::class, 'getAll'])->middleware('jwt.middleware');
        Route::get('/{slug}', [FormsController::class, 'getDetail'])->middleware('jwt.middleware');

        // Question
        Route::post('/{slug}', [QuestionsController::class, 'post'])->middleware('jwt.middleware');
        Route::delete('/{slug}/questions/{id}', [QuestionsController::class, 'delete'])->middleware('jwt.middleware');

        // Response
        Route::post('/{slug}/responses', [ResponsesController::class, 'post'])->middleware('jwt.middleware');
        Route::get('/{slug}/responses', [ResponsesController::class, 'getAll'])->middleware('jwt.middleware');
    });
});
