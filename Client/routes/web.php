<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// PC
Route::get('/', 'App\Http\Controllers\IndexController@index');
Route::get('/index', 'App\Http\Controllers\IndexController@index');
Route::post('/index', 'App\Http\Controllers\IndexController@index');
Route::get('/m_order', 'App\Http\Controllers\IndexController@index');
Route::post('/m_order', 'App\Http\Controllers\IndexController@index');
Route::get('/login', 'App\Http\Controllers\LoginController@index');
Route::get('/order', 'App\Http\Controllers\OrderController@index');
Route::post('/order', 'App\Http\Controllers\OrderController@index');
Route::get('/order/test', 'App\Http\Controllers\OrderController@test');
Route::post('/account', 'App\Http\Controllers\IndexController@account');
Route::post('/order/create', 'App\Http\Controllers\OrderController@create_order');
Route::get('/order/create', 'App\Http\Controllers\OrderController@create_order');
Route::get('/calculator', 'App\Http\Controllers\CalculatorController@index');
Route::get('/rule', 'App\Http\Controllers\RuleController@index');
Route::get('/notice', 'App\Http\Controllers\NoticeController@index');
Route::get('/logs', 'App\Http\Controllers\BalanceLogsController@index');
Route::post('/logs', 'App\Http\Controllers\BalanceLogsController@index');

Route::post('/order/m_create', 'App\Http\Controllers\OrderController@m_create_order');
Route::get('/order/m_create', 'App\Http\Controllers\OrderController@m_create_order');

Route::get('/test', 'App\Http\Controllers\TestController@index');
Route::get('/test/getMinMaxPrice', 'App\Http\Controllers\TestController@getMinMaxPrice');

Route::get('/match', 'App\Http\Controllers\MatchController@index');
Route::post('/match', 'App\Http\Controllers\MatchController@index');

Route::get('/game', 'App\Http\Controllers\GameController@index');

Route::get('/match/list', 'App\Http\Controllers\MatchController@post');
Route::post('/match/list', 'App\Http\Controllers\MatchController@post');

Route::get('/match_list/index', 'App\Http\Controllers\MatchListController@index');

// 暫定錯誤頁
Route::get('/error/404', 'App\Http\Controllers\TestController@error_404');
Route::get('/error/500', 'App\Http\Controllers\TestController@error_500');
Route::get('/error/ip', 'App\Http\Controllers\TestController@error_ip');
Route::get('/error/maintain', 'App\Http\Controllers\TestController@maintain');

// TG通知用
Route::get('/tg', 'App\Http\Controllers\TgBotController@index');
Route::post('/tg', 'App\Http\Controllers\TgBotController@index');

// Mobile
Route::get('/mobile', 'App\Http\Controllers\MobileController@index');
Route::get('/mobile/index', 'App\Http\Controllers\MobileController@index');
Route::get('/mobile/match', 'App\Http\Controllers\MobileController@index');
Route::get('/mobile/game', 'App\Http\Controllers\MobileController@index');
Route::get('/mobile/m_order', 'App\Http\Controllers\MobileController@index');
Route::get('/mobile/logs', 'App\Http\Controllers\MobileController@index');
Route::get('/mobile/result', 'App\Http\Controllers\MobileController@index');

Route::get('/mobile/m_maintain', 'App\Http\Controllers\MobileController@index');
Route::get('/mobile/m_404', 'App\Http\Controllers\MobileController@index');
Route::get('/mobile/m_ip', 'App\Http\Controllers\MobileController@index');
Route::get('/mobile/m_500', 'App\Http\Controllers\MobileController@index');

//API V2
Route::post('/api/v2/common_account',   'App\Http\Controllers\LsportApiController@CommonAccount');
Route::post('/api/v2/index_carousel',   'App\Http\Controllers\LsportApiController@IndexCarousel');
Route::post('/api/v2/index_marquee',    'App\Http\Controllers\LsportApiController@IndexMarquee');
Route::post('/api/v2/index_notice',     'App\Http\Controllers\LsportApiController@IndexNotice');
Route::post('/api/v2/index_match_list', 'App\Http\Controllers\LsportApiController@IndexMatchList');
Route::post('/api/v2/result_index',     'App\Http\Controllers\LsportApiController@ResultIndex');
Route::post('/api/v2/match_index',      'App\Http\Controllers\LsportApiController@MatchIndex');
Route::post('/api/v2/game_index',       'App\Http\Controllers\LsportApiController@GameIndex');
Route::post('/api/v2/match_sport',       'App\Http\Controllers\LsportApiController@MatchSport');
Route::post('/api/v2/balance_logs',     'App\Http\Controllers\LsportApiController@BalanceLogs');
Route::post('/api/v2/game_bet',         'App\Http\Controllers\LsportApiController@GameBet');
Route::post('/api/v2/m_game_bet',       'App\Http\Controllers\LsportApiController@mGameBet');
Route::post('/api/v2/common_order',     'App\Http\Controllers\LsportApiController@CommonOrder');
