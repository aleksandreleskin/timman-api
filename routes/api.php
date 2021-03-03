<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\TodoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('documents', [DocumentsController::class, 'upload']);
    Route::get('documents', [DocumentsController::class, 'getDocuments']);
    Route::get('documents/{id}', [DocumentsController::class, 'downloadDocument']);
    Route::delete('documents/{id}', [DocumentsController::class, 'removeDocument']);
    Route::post('documents/share', [DocumentsController::class, 'shareDocument']);
});
Route::get('documents/share/{id}', [DocumentsController::class, 'getShareDocument']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('notes', [NotesController::class, 'createNote']);
    Route::get('notes', [NotesController::class, 'getNotes']);
    Route::get('notes/{id}', [NotesController::class, 'getNoteValue']);
    Route::put('notes', [NotesController::class, 'saveNote']);
    Route::delete('notes/{id}', [NotesController::class, 'removeNote']);
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('todo', [TodoController::class, 'createTask']);
    Route::get('todo', [TodoController::class, 'getTasks']);
    Route::patch('todo/{id}', [TodoController::class, 'update']);
    Route::put('todo', [TodoController::class, 'changeOrder']);
    Route::delete('todo/{id}', [TodoController::class, 'removeTask']);
});
