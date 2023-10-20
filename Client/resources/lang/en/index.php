<?php
/****************
 * 
 * 	Index 語系檔
 * 
 */
return [
	// 前端
	"bet_area" => [
		'hi' => 'Hi!', // "嗨!"
		'maxwin' => 'Maximum Potential Winnings', // "最高可贏"
		'better_rate' => 'Accept better odds', // "自動接受更好賠率"
		'bet' => 'Bet', // "投注"
		'disabled' => 'Disabled',
		'cancel' => 'Cancel', // "取消"
		'limit' => 'Betting Limit', // "限額"
		'loading' => 'Processing...', // "加载中"
	],
	'mainArea' => [
		'early' => 'Early Odds', // "早盤"
		'living' => 'Live Betting', // "滾球"
		'today' => 'Today', // "今日"
		'time' => 'Game Time: ', // "比賽時間: "
		'homeWin' => 'Home win', // "主勝"
		'awayWin' => 'Away win', // "客勝"
		'tie' => 'Tie', // "平局"
		'cancel' => 'Cancel', // "取消"
		'nogame' => 'No matches currently ', // "目前沒有賽事"
		'nogameitem' => 'No betting options currently ', // "目前沒有投注玩法"
		'nomredata' => 'No more info available', // "沒有更多資料了"
		'homeTeam' => 'Home Team', // "主隊"
		'awayTeam' => 'Away Team', // "客隊"
		'gaming' => 'Match in progress', // "開賽中"
		'notgaming' => 'Not Started', // "未開賽"
		'homeTeamTag' => 'Main', // "主"
		'upperStage' => 'Upper',
		'lowerStage' => 'Lower',
		'stage' => 'Stage',
		'readyToStart' => 'To be start',
		'overtime' => 'Over time',
		'stageArr' => [
			154914 => [
				1 => '1st Inning',
				2 => '2nd Inning',
				3 => '3rd Inning',
				4 => '4th Inning',
				5 => '5th Inning',
				6 => '6th Inning',
				7 => '7th Inning',
				8 => '8th Inning',
				9 => '9th Inning',
				40 => 'Extra Innings',
				62 => 'Error',
				80 => 'Break Time',
				100 => 'Full Time',
				101 => 'Full Time After Extra Time',
			],
			48242 => [
				1 => '1st Quarter',
				2 => '2nd Quarter',
				3 => '3rd Quarter',
				4 => '4th Quarter',
				40 => 'Overtime',
				80 => 'Break Time',
				100 => 'Full Time',
				101 => 'Full Time After Overtime',
			],
			6046 => [
				10 => '1st Half',
				20 => '2nd Half',
				25 => '3rd Half',
				30 => 'Overtime 1st Half',
				35 => 'Overtime 2nd Half',
				50 => 'Penalties',
				80 => 'Break Time',
				100 => 'Full Time',
				101 => 'Full Time After Overtime',
				102 => 'Full Time After Penalties',
			],
			35232 => [
				1 => '1st Inning',
				2 => '2nd Inning',
				3 => '3rd Inning',
				40 => 'Overtime',
				50 => 'Penalties',
				80 => 'Break Time',
				100 => 'Full Time',
				101 => 'Full Time After Overtime',
				102 => 'Full Time After Penalties',
			]
		]
	],
	'm_order' => [
		'morder_detail' => 'Parlay Details', // "串關明細"
		'clear_all_order' => 'Clear Bet Slip', // "清除注單"
		'max_ten' => 'Up to 10 selections per slip!', // "一次最多串10注!"
		'at_least_two' => 'A minimum of 2 selections for a parlay slip!', // "串關至少兩注!"
	],
	'js' => [
		'websocket_connect_err' => 'WebSocket connection error:', // "WebSocket連接錯誤"
		'big' => 'Big', // "大"
		'small' => 'Small', // "小"
		'limit' => 'Limit', // "限額"
		'no_bet_amout' => 'Place bet first!', // "請先下注!"
		'tooless_bet_amout' => 'minimum bet', // "最低投注"
		'toohigh_bet_amout' => 'maximum bet', // "最高投注"
	],
	'priorityArr' => [
		'allwin' => [1, 2, 9, 101, 102, 109, 113, 117, 121, 201, 202, 301],
		'hcap' => [3, 4, 103, 104, 110, 114, 118, 122, 203 , 204, 302],
		'oddeven' => [7, 107, 112, 116, 120, 124, 304],
		'size' => [5, 205, 206, 105, 106, 111, 115, 119, 123, 303]
	],
	'sportBetData' => [
		6046 => [
			'gameTitle' => ['Full-time winner', 'Full-time handicap', 'Full-time over/under', 'Half-time winner', 'Half-time handicap', 'Half-time over/under'],
			'mainPriorityArr' => [201, 203, 205, 202, 204, 206],
		],
		48242 => [
			'gameTitle' => ['Full-time winner', 'Full-time handicap', 'Full-time over/under', 'Full-time Odd/Even'],
			'mainPriorityArr' => [101, 103, 105, 107],
			'stageTitle' => [
				1 => ['1st quarter winner', '1st quarter hcap', '1st quarter o/u', '1st quarter odd/even'],
				2 => ['2nd quarter winner', '2nd quarter hcap', '2nd quarter o/u', '2nd quarter odd/even'],
				3 => ['3rd quarter winner', '3rd quarter hcap', '3rd quarter o/u', '3rd quarter odd/even'],
				4 => ['4th quarter winner', '4th quarter hcap', '4th quarter o/u', '4th quarter odd/even'],
			],
			'stagePriorityArr' => [
				1 => [109, 110, 111, 112],
				2 => [113, 114, 115, 116],
				3 => [117, 118, 119, 120],
				4 => [121, 122, 123, 124]
			],
		],
		154914 => [
			'gameTitle' => ['Full-time winner', 'Full-time handicap', 'Full-time over/under', 'Full-time Odd/Even'],
			'mainPriorityArr' => [1, 3, 5, 7],
		],
		35232 => [
			'gameTitle' => ['Full-time winner', 'Full-time handicap', 'Full-time over/under', 'Full-time Odd/Even'],
			'mainPriorityArr' => [301, 302, 303, 304],
		],
	],
	'tableLivingData' => [
		"fulltimescore" => "Total score", // "總分",
		"firsthalfscore" => "1st-half", // "上半場比分",
		"secondhalfscore" => "2nd-half", // "下半場比分",
		"firstquarter" => "1st quarter ", // "第一節比分",
		"secondquarter" => "2nd quarter", //  "第二節比分",
		"thirdquarter" => "3rd quarter", // "第三節比分",
		"fourthquarter" => "4th quarter", // "第四節比分",
		"firstround" => "1st inning", // "1局",
		"secondgame" => "2nd inning",  //"2局",
		"thirdinning" => "3rd inning",  // "3局", 
		"fourthinning" => "4th inning", // "4局",
		"fifthinning" => "5th inning",  // "5局",
		"sixthinning" => "6th inning",  // "6局", 
		"seventhinning" => "7th inning",  // "7局", 
		"eighthinning" => "8th inning",  // "8局", 
		"ninthinning" => "9th inning",  // "9局", 
		"tenthinning" => "10th inning", // "10局", 
		"eleventhinning" => "11th inning",  //  "11局",
		"twelfthinning" => "12th inning",  // "12局",
	],
];