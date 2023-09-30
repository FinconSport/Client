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
		'cancel' => 'Cancel', // "取消"
		'limit' => 'Betting Limit', // "限額"
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
		'statusArr' => [
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
				100 => 'Full Time',
				101 => 'Full Time After Extra Time',
			],
			48242 => [
				1 => '1st Quarter',
				2 => '2nd Quarter',
				3 => '3rd Quarter',
				4 => '4th Quarter',
				40 => 'Overtime',
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
		1 => ['5', '6', '14', '18', '22', '26', '28'],  // 獨贏系列 - win-only series
		2 => ['1', '3', '12', '16', '20', '24', '27'],  // 大小系列 - over/under series
		3 => ['2', '4', '9', '10', '11', '15', '19', '23', '29']  // 讓球系列 - handicap series
	],
	'game_priority' => [
		1 => "Full game over/under", // Generic        // 1 => "全場大小",   // 通用
        2 => "Full game point spread", // Generic       // 2 => "全場讓球",   // 通用
        3 => "1st half over/under", // Generic        // 3 => "上半場大小",   // 通用
        4 => "1st half point spread", // Generic        // 4 => "上半場讓球",   // 通用
        5 => "Full game winner", // Generic        // 5 => "全場獨贏",   // 通用
        6 => "1st half winner", // Generic        // 6 => "上半場獨贏",   // 通用
        7 => "Full game correct score", // soccer        // 7 => "全場波膽", // 足
        8 => "1st half correct score", // soccer        // 8 => "上半場-波膽", // 足
        9 => "Full game point spread", // basketball        // 9 => "全場讓分", // 籃
        10 => "1st half point spread", // basketball        // 10 => "上半場讓分", // 籃
        11 => "1st quarter point spread", // basketball        // 11 => "第1節讓分", // 籃
        12 => "1st quarter over/under", // basketball        // 12 => "第1節大小", // 籃
        13 => "1st quarter odd/even", // basketball        // 13 => "第1節單/雙", // 籃
        14 => "1st quarter winner", // basketball        // 14 => "第1節獨贏", // 籃
        15 => "2nd quarter point spread", // basketball        // 15 => "第2節讓分", // 籃
        16 => "2nd quarter over/under", // basketball        // 16 => "第2節大小", // 籃
        17 => "2nd quarter odd/even", // basketball        // 17 => "第2節單/雙", // 籃
        18 => "2nd quarter winner", // basketball        // 18 => "第2節獨贏", // 籃
        19 => "3rd quarter point spread", // basketball        // 19 => "第3節讓分", // 籃
        20 => "3rd quarter over/under", // basketball        // 20 => "第3節大小", // 籃
        21 => "3rd quarter odd/even", // basketball        // 21 => "第3節單/雙", // 籃
        22 => "3rd quarter winner", // basketball        // 22 => "第3節獨贏", // 籃
        23 => "4th quarter point spread", // basketball        // 23 => "第4節讓分", // 籃
        24 => "4th quarter over/under", // basketball        // 24 => "第4節大小", // 籃
        25 => "4th quarter odd/even", // basketball        // 25 => "第4節單/雙", // 籃
        26 => "4th quarter winner", // basketball        // 26 => "第4節獨贏", // 籃
        27 => "1st 5 innings-Over/under", // baseball       // 27 => "前5局 - 大小", // 棒
        28 => "1st 5 innings-Win/draw/lose", // baseball        // 28 => "前5局 - 勝平負", // 棒
        29 => "1st 5 innings-Handicap", // baseball        // 29 => "前5局 - 讓球", // 棒
	],
	'sportBetData' => [
		6046 => [
			'gameTitle' => ['Full-time winner', 'Full-time handicap', 'Full-time over/under', 'Half-time winner', 'Half-time handicap', 'Half-time over/under'],
			'priorityArr' => [13, 15, 17, 14, 16, 18],
			'stage' => ['1st half', '2nd half', 'Overtime)'],
			'ws' => 'wss://soccer.asgame.net/ws'
		],
		48242 => [
			'gameTitle' => ['Full-time winner', 'Full-time handicap', 'Full-time over/under', '1st-half winner', '1st-half handicap', '1st-half over/under'],
			'priorityArr' => [7, 9, 11, 8, 10, 12],
			'stage' => ['1st quarter', '2nd quarter', '3rd quarter', '4th quarter', 'Overtime'],
			'ws' => 'wss://basketball.asgame.net/ws'
		],
		154914 => [
			'gameTitle' => ['Full-time winner', 'Full-time handicap', 'Full-time over/under', '1st 5 innings winner', '1st 5 innings handicap', '1st 5 innings over/under'],
			'priorityArr' => [1, 3, 5, 2, 4, 6],
			'stage' => ['1st Stage','2nd Stage','3rd Stage','4st Stage','5st Stage','6st Stage','7st Stage','8st Stage','9st Stage','Overtime'],
			'ws' => 'wss://baseball.asgame.net/ws'
		],
		4 => [
			'gameTitle' => ['Full-time winner', '全場讓球', '全場大小', '半場獨贏', '半場讓球', '半場大小', '所有', '獨贏', '讓球', '大小'], // ['全場獨贏', '全場讓球', '全場大小', '半場獨贏', '半場讓球', '半場大小', '所有', '獨贏', '讓球', '大小']
			'priorityArr' => ['5', '2', '1', '6', '4', '3']
		],
		5 => [
			'gameTitle' => ['Full-time winner', '全場讓球', '全場大小', '半場獨贏', '半場讓球', '半場大小', '所有', '獨贏', '讓球', '大小'], // ['全場獨贏', '全場讓球', '全場大小', '半場獨贏', '半場讓球', '半場大小', '所有', '獨贏', '讓球', '大小']
			'priorityArr' => ['5', '2', '1', '6', '4', '3']
		],
		6 => [
			'gameTitle' => ['Full-time winner', '全場讓球', '全場大小', '半場獨贏', '半場讓球', '半場大小', '所有', '獨贏', '讓球', '大小'], // ['全場獨贏', '全場讓球', '全場大小', '半場獨贏', '半場讓球', '半場大小', '所有', '獨贏', '讓球', '大小']
			'priorityArr' => ['5', '2', '1', '6', '4', '3']
		],
		7 => [
			'gameTitle' => ['Full-time winner', '全場讓球', '全場大小', '半場獨贏', '半場讓球', '半場大小', '所有', '獨贏', '讓球', '大小'], // ['全場獨贏', '全場讓球', '全場大小', '半場獨贏', '半場讓球', '半場大小', '所有', '獨贏', '讓球', '大小']
			'priorityArr' => ['5', '2', '1', '6', '4', '3']
		],
		8 => [
			'gameTitle' => ['Full-time winner', '全場讓球', '全場大小', '半場獨贏', '半場讓球', '半場大小', '所有', '獨贏', '讓球', '大小'], // ['全場獨贏', '全場讓球', '全場大小', '半場獨贏', '半場讓球', '半場大小', '所有', '獨贏', '讓球', '大小']
			'priorityArr' => ['5', '2', '1', '6', '4', '3']
		],
		9 => [
			'gameTitle' => ['Full-time winner', '全場讓球', '全場大小', '半場獨贏', '半場讓球', '半場大小', '所有', '獨贏', '讓球', '大小'], // ['全場獨贏', '全場讓球', '全場大小', '半場獨贏', '半場讓球', '半場大小', '所有', '獨贏', '讓球', '大小']
			'priorityArr' => ['5', '2', '1', '6', '4', '3']
		]
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