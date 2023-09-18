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

Route::get('/match', 'App\Http\Controllers\MatchController@index');
Route::post('/match', 'App\Http\Controllers\MatchController@index');

Route::get('/match/list', 'App\Http\Controllers\MatchController@post');
Route::post('/match/list', 'App\Http\Controllers\MatchController@post');

Route::get('/match_list/index', 'App\Http\Controllers\MatchListController@index');

// 暫定錯誤頁
Route::get('/error/404', 'App\Http\Controllers\TestController@error_404');
Route::get('/error/500', 'App\Http\Controllers\TestController@error_500');
Route::get('/error/ip', 'App\Http\Controllers\TestController@error_ip');
Route::get('/error/maintain', 'App\Http\Controllers\TestController@maintain');

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

// ClientApi

// 第二版, ant 
Route::post('/api/v1/common_account', 'App\Http\Controllers\ApiController@CommonAccount');
Route::post('/api/v1/index_carousel', 'App\Http\Controllers\ApiController@IndexCarousel');
Route::post('/api/v1/index_marquee', 'App\Http\Controllers\ApiController@IndexMarquee');
Route::post('/api/v1/index_notice', 'App\Http\Controllers\ApiController@IndexNotice');
Route::post('/api/v1/index_match_list', 'App\Http\Controllers\ApiController@IndexMatchList');
Route::post('/api/v1/result_index', 'App\Http\Controllers\ApiController@ResultIndex');
Route::post('/api/v1/match_index', 'App\Http\Controllers\ApiController@MatchIndex');
Route::post('/api/v1/match_sport', 'App\Http\Controllers\ApiController@MatchSport');
Route::post('/api/v1/game_index', 'App\Http\Controllers\ApiController@GameIndex');
Route::post('/api/v1/game_bet', 'App\Http\Controllers\ApiController@GameBet');
Route::post('/api/v1/m_game_bet', 'App\Http\Controllers\ApiController@mGameBet');
Route::post('/api/v1/common_order', 'App\Http\Controllers\ApiController@CommonOrder');
Route::post('/api/v1/balance_logs', 'App\Http\Controllers\ApiController@BalanceLogs');

//API V2, LSport
Route::post('/api/v2/common_account', 'App\Http\Controllers\ApiController@CommonAccount');
Route::post('/api/v2/index_carousel', 'App\Http\Controllers\ApiController@IndexCarousel');
Route::post('/api/v2/index_marquee', 'App\Http\Controllers\ApiController@IndexMarquee');
Route::post('/api/v2/index_notice', 'App\Http\Controllers\ApiController@IndexNotice');
Route::post('/api/v2/index_match_list', 'App\Http\Controllers\ApiController@IndexMatchList');
Route::post('/api/v2/result_index', 'App\Http\Controllers\ApiController@ResultIndex');
Route::post('/api/v2/match_index', 'App\Http\Controllers\ApiController@MatchIndex');
Route::post('/api/v2/match_sport', 'App\Http\Controllers\ApiController@MatchSport');
Route::post('/api/v2/game_index', 'App\Http\Controllers\ApiController@GameIndex');
Route::post('/api/v2/balance_logs', 'App\Http\Controllers\ApiController@BalanceLogs');

Route::post('/api/v2/game_bet', 'App\Http\Controllers\ApiController@GameBet');
Route::post('/api/v2/m_game_bet', 'App\Http\Controllers\ApiController@mGameBet');
Route::post('/api/v2/common_order', 'App\Http\Controllers\ApiController@CommonOrder');