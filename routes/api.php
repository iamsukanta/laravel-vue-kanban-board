<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([ 'middleware' => ['api', 'token.verify'], 'namespace' => 'App\Http\Controllers\Api'], function () {
    Route::get('columns', 'ColumnsController@index');
    Route::post('columns', 'ColumnsController@store');
    Route::delete('columns/{id}', 'ColumnsController@destroy');

    Route::post('columns/{id}/cards', 'ColumnsController@storeCard');
    Route::get('columns/{id}/cards', 'ColumnsController@getCards');
    Route::put('columns/{id}/cards/{cardId}', 'ColumnsController@updateCard');

    Route::get('export/db', 'ColumnsController@exportDB');
});
