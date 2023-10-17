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

		$DSLQuery = [
			"query" => [
				"bool" => [
					"must" => [
						"script" : [
							"script" : [
								"source" => 'doc["m_id"].value == doc["id"].value'
							]
						],
						"term" : [
							"player_id" => [
								"value" : 9 
							]
						]
					],
					"should" => [
						"term" : [
							"status" => [
								"value" : 4
							]
						]
					]
				]
			],
			"sort" => [
				["id" : "desc"]
			],
			"from" : 0,
			"size" : 0
		];

		echo json_encode($DSLQuery);

	}

}
