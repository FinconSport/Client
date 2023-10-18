<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GameOrder extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "game_order";

	protected static function getOrderList($data) {

        // 緩存時間
        $cacheAliveTime = 10;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {
			
			$player_id = $data['player_id'];
			$result = $data['result'];
			$skip = $data['skip'];
			$page_limit = $data['page_limit'];
			
            $model = self::where('player_id', $player_id)->whereColumn('m_id', '=', 'id');

			if ($result == -1) {
				// do nothing
			} elseif ($result == 0) {
				$model = $model->whereIn('status', [0,1,2,3]);
			} else {
				$model = $model->where('status', 4);
			}

			$return = $model->skip($skip)->take($page_limit)->orderBy("id","DESC")->get();
            
            return $return;
        });
	}

	protected static function ddddgetOrderList($input) {

		$player_id = $input['player_id'];
		$result = $input['result'];
		$skip = $input['skip'];
		$page_limit = $input['page_limit'];

		$DSLQuery = [
			"query" => [
				"bool" => [
					"must" => [
						["script" => [
							"script" => [
								"source" => 'doc["m_id"].value == doc["id"].value'
							]
						]]
					]
				]
			],
			"script_fields" => [
				"formatted_price" => [
					"script" => [
				  		"source" => "Math.round(doc['bet_rate'].value * 100) / 100",
				  		"lang" => "painless"
					]
				],
				"formatted_price" => [
					"script" => [
				  		"source" => "Math.round(doc['player_rate'].value * 100) / 100",
				  		"lang" => "painless"
					]
				],
			],
			"sort" => [
				["id" => "desc"]
			],
			"from" => $skip,
			"size" => $page_limit,
			"_source" => [
				"id",
				"m_id",
				"m_order",
				"agent_id",
				"agent_name",
				"player_id",
				"player_name",
				"currency_type",
				"league_id",
				"league_name",
				"sport_id",
				"fixture_id",
				"market_id",
				"market_name",
				"market_bet_id",
				"market_bet_name",
				"market_bet_line",
				"home_team_id",
				"home_team_name",
				"away_team_id",
				"away_team_name",
				"home_team_score",
				"away_team_score",
				"market_priority",
				"bet_amount",
				"bet_rate",
				"player_rate",
				"better_rate",
				"active_bet",
				"result_amount",
				"result_percent",
				"create_time",
				"approval_time",
				"delay_time",
				"result_time",
				"is_result",
				"status"]
		];
		

		$DSLQuery['from'] = $skip;
		$DSLQuery['size'] = $page_limit;
		$DSLQuery['query']['bool']['must'][] = ["term" => ["player_id" => ["value" => $player_id]]];
		
		if ($result == 0) {
			$DSLQuery['query']['bool']['must'][] = ["term" => ["status" => [0,1,2,3]]];
		} else {
			$DSLQuery['query']['bool']['must'][] = ["term" => ["status" => ["value" => 4]]];
		}
		
		$DSLQueryStr = json_encode($DSLQuery,true);

		$return = self::queries($DSLQueryStr);

		if ($return === false) {
			
		dd($DSLQueryStr);
			return false;
		}

		// 重整格式
		$data = array();
		foreach ($return['hits']['hits'] as $k => $v) {
			$data[] = $v['_source'];
		}

		dd($DSLQueryStr,$data);

	}

}
