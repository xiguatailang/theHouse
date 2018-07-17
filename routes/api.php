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
//Route::post('/login', function (){
//    return response('hello world enen' ,200)->cookie('login_in',1101,10);
//});


Route::any('/house/{method}', 'HouseController@distributor')->middleware('filter');


Route::get('/loginTest', function (){
    return response('hello world' ,200)->cookie('login_in',1101,10);
});


