<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use DB;
use App\Models\Player;
use App\Models\LsportFixture;
use App\Models\LsportSport;

class TestController extends PcController {
    
    // 首頁
    public function index(Request $request) {
    
      // list方法 , 同get方法
      $return = Player::where("status",1)->where("currency_type",1)->list();

      // fetch方法 , 同first 方法
      $return = Player::where("status",1)->where("id",1)->where("currency_type",1)->fetch();

      // cc方法 , 同count 方法
      $return = Player::where("status",1)->where("currency_type",1)->cc();

      // total方法, 專門用於取得統計
      $return = Player::select('agent_id', DB::raw('SUM(balance) as total_balance'), DB::raw('COUNT(*) as player_count'))->groupBy('agent_id')->total();

      // 多表關聯搜尋 , 需要用別名
      $return = LsportFixture::select('f.*')
      ->from('es_lsport_fixture as f')
      ->join('es_lsport_sport as s', 'f.sport_id', '=', 's.sport_id')
      ->where('s.status', '=', 1)
      ->skip(100)->take(10)
      ->orderBy("f.start_time","DESC")
      ->list();

      ////////////////////////

      dd($return);
  
      if ($return === false) {

      }
      
    }

    public function getMinMaxPrice(Request $request) {
        
    	$input = $this->getRequest($request);

		  $session = Session::all();

      /////////////////////////
      // 构建 Elasticsearch 查询 DSL
      $fixtureId = $input['fixture_id'];

// 构建 Elasticsearch 查询 DSL
$query = [
    'size' => 0,
    'query' => [
        'term' => [
            'fixture_id' => $fixtureId,
        ],
    ],
    'aggs' => [
        'group_by_market' => [
            'terms' => [
                'field' => 'market_id',
                'size' => 10000, // 根据你的数据量适当调整
            ],
            'aggs' => [
                'group_by_base_line' => [
                    'terms' => [
                        'field' => 'base_line.keyword', // 使用 keyword 类型字段
                        'size' => 10000, // 根据你的数据量适当调整
                    ],
                    'aggs' => [
                        'min_price' => [
                            'min' => [
                                'field' => 'price_keyword', // 使用新的 keyword 字段
                            ],
                        ],
                        'max_price' => [
                            'max' => [
                                'field' => 'price_keyword', // 使用新的 keyword 字段
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];

      // 构建 Basic Authentication 头部
      $username = 'devuser';
      $password = '1hqXxl0YAXd2HAjiTc4X';
      $credentials = base64_encode($username . ':' . $password);
      $headers = [
          'Authorization' => 'Basic ' . $credentials,
          'Content-Type'  => 'application/json',
          'Host'          => 'sportc.asgame.net',
      ];

      // 发送 Elasticsearch 查询请求，包括身份验证头部
      $response = Http::withHeaders($headers)
          ->post('http://72.167.135.22:29200/es_lsport_market_bet/_search', $query);

      // 解析 Elasticsearch 响应
      $data = $response->json();
      
      dd($headers,$response, $data, $query);
      // 获取 "buckets"
      $buckets = $data['aggregations']['composite_agg']['buckets'];

      // 处理 "buckets" 数据
      $results = [];
      foreach ($buckets as $bucket) {
          $fixtureId = $bucket['key']['fixture_id'];
          $marketId = $bucket['key']['market_id'];
          $baseLine = $bucket['key']['base_line'];
          $maxPrice = $bucket['max_price']['value'];
          $minPrice = $bucket['min_price']['value'];

          // 将结果存储在数组中
          $results[] = [
              'fixture_id' => $fixtureId,
              'market_id' => $marketId,
              'base_line' => $baseLine,
              'max_price' => $maxPrice,
              'min_price' => $minPrice,
          ];
      }

      dd($results);
  }

}