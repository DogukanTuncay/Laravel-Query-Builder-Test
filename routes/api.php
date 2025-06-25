<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('courses')->group(function () {
    Route::get('/', [CourseController::class, 'index']);
    Route::post('/', [CourseController::class, 'store']);
    Route::get('{id}', [CourseController::class, 'show']);
    Route::put('{id}', [CourseController::class, 'update']);
    Route::delete('{id}', [CourseController::class, 'destroy']);
});
