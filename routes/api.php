<?php

use Illuminate\Http\Request;
use App\Help\UserFilter;
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


Route::post('/register', 'HouseController@register');
Route::post('/login', 'HouseController@login');
Route::group(['middleware','filter'] ,function (){
    Route::any('/house/{method}', 'HouseController@distributor');
});


