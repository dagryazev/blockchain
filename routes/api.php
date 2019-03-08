<?php

use Illuminate\Http\Request;

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

Route::post('auth/register', 'UserController@register');
Route::post('auth/login', 'UserController@authenticate');
Route::get('open', 'DataController@open');

Route::group(['middleware' => ['jwt.verify']], function() {
  Route::get('user', 'UserController@getAuthenticatedUser');
  Route::post('auth/logout', 'UserController@logout');
});

Route::post('wallet/', 'WalletController@create'); //Создание кошелька
Route::delete('wallet/{wallet_id}', 'WalletController@delete'); //Удаление кошелька (protected)

Route::post('wallet/payment/in/{wallet_id}/{coins}', 'WalletController@payment_in'); //Пополнение счета (protected)
Route::post('wallet/payment/out/{wallet_id}/{coins}', 'WalletController@payment_out'); //Вывод средств (protected)

Route::post('wallet/delegate/{wallet_id}/{server_id}/{coins}', 'WalletController@delegate'); //Делегирование (protected)
Route::post('wallet/undelegate/{wallet_id}/{server_id}/{coins}', 'WalletController@undelegate'); //Анделигирование (protected)

Route::post('wallet/reward/{wallet_id}/{server_id}/{coins}', 'WalletController@reward'); //Награда

Route::post('servers/{address}/{server_name}', 'ServersController@create'); //Создание сервера
Route::delete('servers/{server_id}', 'ServersController@delete'); //Удаление сервера

Route::get('wallet/', 'WalletController@getAll');  //Получение списка кошелька пользователя
Route::get('wallet/{wallet_id}/transaction/', 'WalletController@getAllTransaction'); //Получение транзакций кошелька
Route::post('wallet/transaction/', 'WalletController@getAllTransaction');
Route::get('servers/', 'ServersController@getAll'); //Получение списка серверов
//
//
//
//
//
