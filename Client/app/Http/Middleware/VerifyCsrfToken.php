<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
            '/tg',
            '/tg/josh',

            '/index/match_list',
            
            '/api/common_account',
            '/api/common_order',
            '/api/index_carousel',
            '/api/index_marquee', 
            '/api/index_match_list',
            '/api/match_index',
            '/api/match_list',
            '/api/game_index',
            '/api/game_bet',

            //////////////////////////

            '/api/v1/common_account',
            '/api/v1/common_order',  
            '/api/v1/index_carousel',
            '/api/v1/index_marquee', 
            '/api/v1/index_notice', 
            '/api/v1/index_match_list', 
            '/api/v1/result_index', 
            '/api/v1/match_index',
            '/api/v1/match_sport',
            '/api/v1/match_list',
            '/api/v1/game_index',
            '/api/v1/game_bet',
            '/api/v1/m_game_bet',
            '/api/v1/balance_logs',
            //////////////////////////

            '/api/v2/common_account',
            '/api/v2/common_order',  
            '/api/v2/index_carousel',
            '/api/v2/index_marquee', 
            '/api/v2/index_notice', 
            '/api/v2/index_match_list', 
            '/api/v2/result_index', 
            '/api/v2/match_index',
            '/api/v2/match_sport',
            '/api/v2/match_list',
            '/api/v2/game_index',
            '/api/v2/game_bet',
            '/api/v2/m_game_bet',
            '/api/v2/balance_logs',

            //////////////////////////

            '/api/test'
    ];
}
