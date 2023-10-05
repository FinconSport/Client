<?php
/****************
 * 
 * 	Index 語系檔
 * 
 */
return [
	// 前端
	"bet_area" => [
		'hi' => '嗨!',
		'maxwin' => '最高可贏',
		'better_rate' => '自動接受更好賠率',
		'bet' => '投注',
		'cancel' => '取消',
		'limit' => '限額',
	],
	'mainArea' => [
		'early' => '早盤',
		'living' => '滾球',
		'today' => '今日',
		'time' => '比賽時間: ',
		'homeWin' => '主勝',
		'awayWin' => '客勝',
		'tie' => '平局',
		'cancel' => '取消',
		'nogame' => '目前沒有賽事',
		'nogameitem' => '目前沒有投注玩法',
		'nomredata' => '沒有更多資料了',
		'homeTeam' => '主隊',
		'awayTeam' => '客隊',
		'gaming' => '開賽中',
		'notgaming' => '未開賽',
		'homeTeamTag' => '主',
		'upperStage' => '上',
		'lowerStage' => '下',
		'stage' => '局',
		'readyToStart' => '即將開賽',
		'overtime' => '加時賽',
		'stageArr' => [
			154914 => [
				1 => '1局',
				2 => '2局',
				3 => '3局',
				4 => '4局',
				5 => '5局',
				6 => '6局',
				7 => '7局',
				8 => '8局',
				9 => '9局',
				40 => '加時賽',
				62 => '錯誤',
				100 => '比賽結束',
				101 => '加時賽結束',
			],
			48242 => [
				1 => '第 1 節',
				2 => '第 2 節',
				3 => '第 3 節',
				4 => '第 4 節',
				40 => '加時賽',
				100 => '比賽結束',
				101 => '加時賽結束',
			],
			6046 => [
				10 => '上半場',
				20 => '下半場',
				25 => '第三半場',
				30 => '加時賽 上半場',
				35 => '加時賽 下半場',
				50 => '點球',
				100 => '比賽結束',
				101 => '加時賽結束',
				102 => '點球結束'
			]
		]
	],
	'm_order' => [
		'morder_detail' => '串關明細',
		'clear_all_order' => '清除注單',
		'max_ten' => '一次最多串10注!',
		'at_least_two' => '串關至少兩注!',
	],
	'js' => [
		'websocket_connect_err' => 'WebSocket連接錯誤:',
		'big' => '大',
		'small' => '小',
		'limit' => '限額',
		'no_bet_amout' => '請先下注!',
		'tooless_bet_amout' => '最低投注',
		'toohigh_bet_amout' => '最高投注',
	],
	'sportBetData' => [
		6046 => [
			'gameTitle' => ['全場獨贏', '全場讓球', '全場大小', '上半場獨贏', '上半場讓球', '上半場大小'],
			'mainPriorityArr' => [201, 203, 205, 202, 204, 206],
			'stage' => ['上半場', '下半場', '延長賽'],
			'ws' => 'wss://soccer.asgame.net/ws'
		],
		48242 => [
			'gameTitle' => ['全場獨贏', '全場讓分', '全場大小', '上半場獨贏', '上半場讓分', '上半場大小'],
			'mainPriorityArr' => [101, 103, 105, 102, 104, 106],
			'stage' => ['第一節', '第二節', '第三節', '第四節', '延長賽'],
			'ws' => 'wss://basketball.asgame.net/ws'
		],
		154914 => [
			'gameTitle' => ['全場獨贏', '全場讓球', '全場大小', '全場單雙'],
			'mainPriorityArr' => [1, 3, 5, 7],
			'stage' => ['第一局', '第二局', '第三局', '第四局', '第五局', '第六局', '第七局', '第八局', '第九局', '延長賽'],
			'ws' => 'wss://baseball.asgame.net/ws'
		],
	],
	'tableLivingData' => [
		"fulltimescore" => "總分",
		"firsthalfscore" => "上半場比分",
		"secondhalfscore" => "下半場比分",
		"firstquarter" => "第一節比分",
		"secondquarter" => "第二節比分",
		"thirdquarter" => "第三節比分",
		"fourthquarter" => "第四節比分",
		"firstround" => "1局",
		"secondgame" => "2局", 
		"thirdinning" => "3局", 
		"fourthinning" => "4局", 
		"fifthinning" => "5局", 
		"sixthinning" => "6局", 
		"seventhinning" => "7局", 
		"eighthinning" => "8局", 
		"ninthinning" => "9局", 
		"tenthinning" => "10局", 
		"eleventhinning" => "11局", 
		"twelfthinning" => "12局", 
	],
];