<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\NotesController;

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
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('notes', [NotesController::class, 'create']);
    Route::get('notes', [NotesController::class, 'getNotes']);
    Route::get('notes/{id}', [NotesController::class, 'getNoteValue']);
    Route::put('notes', [NotesController::class, 'save']);
//    Route::get('documents', [DocumentsController::class, 'getDocuments']);
//    Route::get('documents/{id}', [DocumentsController::class, 'downloadDocument']);
//    Route::delete('documents/{id}', [DocumentsController::class, 'removeDocument']);
});
