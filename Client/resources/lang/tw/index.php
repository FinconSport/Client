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
		'disabled' => '已失效',
		'cancel' => '取消',
		'limit' => '限額',
		'loading' => '確認中...',
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
		],
		131506 => [
			'gameTitle' => ['全場獨贏', '全場讓分', '全場大小'],
			'mainPriorityArr' => [401, 403, 405],
		],
		48242 => [
			'gameTitle' => ['全場獨贏', '全場讓分', '全場大小', '全場單雙'],
			'mainPriorityArr' => [101, 103, 105, 107],
			'stageTitle' => [
				1 => ['第一節獨贏', '第一節讓分', '第一節大小', '第一節單雙'],
				2 => ['第二節獨贏', '第二節讓分', '第二節大小', '第二節單雙'],
				3 => ['第三節獨贏', '第三節讓分', '第三節大小', '第三節單雙'],
				4 => ['第四節獨贏', '第四節讓分', '第四節大小', '第四節單雙'],
			],
			'stagePriorityArr' => [
				1 => [109, 110, 111, 112],
				2 => [113, 114, 115, 116],
				3 => [117, 118, 119, 120],
				4 => [121, 122, 123, 124]
			],
		],
		154914 => [
			'gameTitle' => ['全場獨贏', '全場讓球', '全場大小', '全場單雙'],
			'mainPriorityArr' => [1, 3, 5, 7],
		],
		35232 => [
			'gameTitle' => ['全場獨贏', '全場讓球', '全場大小'],
			'mainPriorityArr' => [301, 302, 303],
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