<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameOrder extends Model
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "game_order";

	protected static function getOrderList($input) {

		$DSLQuery = [
			"query" => [
				"bool" => [
					"must" => [
						["script" => [
							"script" => [
								"source" => 'doc["m_id"].value == doc["id"].value'
							]
						]],
						["term" => [
							"player_id" => [
								"value" => 9 
							]
						]]
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
			"from" => 0,
			"size" => 25
		];

		$DSKQueryStr = json_encode($DSLQuery,true);

		dd($DSKQueryStr);

	}

}
