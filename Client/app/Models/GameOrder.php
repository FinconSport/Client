<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameOrder extends Model
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "game_order";

	protected static function getOrderTotal($input) {

		$player_id = $input['player'];
		$DSLQuery = [
			"query" => [
				"bool" => [
					"must" => [
						"script" => [
							"script" => [
								"source" => 'doc["m_id"].value == doc["id"].value'
							]
						],
						"term" => [
							"player_id" => [
								"value" => $player_id
							]
						]
					],
					"should" => [
						"term" => [
							"status" => [
								"value" => 4
							]
						]
					]
				]
			],
			"aggs" => [
				"total_bet_amount" => [
					"sum" => [
						"field" => "bet_amount"
					]
				],
				"total_result_amount" => [
					"sum" => [
						"field" => "result_amount"
					]
				]
			],
			"size" => 0
		];

		echo json_encode($DSLQuery);

	}

}
