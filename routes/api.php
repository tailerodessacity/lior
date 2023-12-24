<?php

use App\Enums\Roles;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\PostsController;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::group(['middleware' => ['api']], function () {
    Route::get('posts', [PostsController::class, 'index']);
    Route::post('posts', [PostsController::class, 'store']);
    Route::patch('posts/{post}', [PostsController::class, 'update']);
    Route::delete('posts/{post}', [PostsController::class, 'destroy']);
    Route::get('posts/{post}', [PostsController::class, 'show']);
});

Route::group(['middleware' => ['api']], function () {
    Route::get('posts/{post}/comments', [CommentsController::class, 'index']);
    Route::post('posts/{post}/comment', [CommentsController::class, 'store']);
    Route::patch('comment/{comment}', [CommentsController::class, 'update']);
    Route::delete('comment/{comment}', [CommentsController::class, 'destroy']);
});





