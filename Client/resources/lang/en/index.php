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
		'better_rate' => 'automatically accept better odds', // "自動接受更好賠率"
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
		'nogame' => 'There are currently no matches', // "目前沒有賽事"
		'nogameitem' => 'There are currently no betting options', // "目前沒有投注玩法"
		'nomredata' => 'There is no more information available', // "沒有更多資料了"
		'homeTeam' => 'Home Team', // "主隊"
		'awayTeam' => 'Away Team', // "客隊"
		'gaming' => 'Match in progress', // "開賽中"
		'notgaming' => 'Not Started', // "未開賽"
		'homeTeamTag' => 'Main' // "主"
	],
	'm_order' => [
		'morder_detail' => 'Parlay Details', // "串關明細"
		'clear_all_order' => 'Clear Bet Slip', // "清除注單"
		'max_ten' => 'Up to 10 selections per bet!', // "一次最多串10注!"
		'at_least_two' => 'A minimum of two selections for a parlay!', // "串關至少兩注!"
	],
	'js' => [
		'websocket_connect_err' => 'WebSocket connection error:', // "WebSocket連接錯誤"
		'big' => 'Big', // "大"
		'small' => 'Small', // "小"
		'limit' => 'Limit', // "限額"
		'no_bet_amout' => 'Please place your bet first!', // "請先下注!"
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
        3 => "First half over/under", // Generic        // 3 => "上半場大小",   // 通用
        4 => "First half point spread", // Generic        // 4 => "上半場讓球",   // 通用
        5 => "Full game winner", // Generic        // 5 => "全場獨贏",   // 通用
        6 => "First half winner", // Generic        // 6 => "上半場獨贏",   // 通用
        7 => "Full game correct score", // soccer        // 7 => "全場波膽", // 足
        8 => "First half correct score", // soccer        // 8 => "上半場-波膽", // 足
        9 => "Full game point spread", // basketball        // 9 => "全場讓分", // 籃
        10 => "First half point spread", // basketball        // 10 => "上半場讓分", // 籃
        11 => "First quarter point spread", // basketball        // 11 => "第1節讓分", // 籃
        12 => "First quarter over/under", // basketball        // 12 => "第1節大小", // 籃
        13 => "First quarter odd/even", // basketball        // 13 => "第1節單/雙", // 籃
        14 => "First quarter winner", // basketball        // 14 => "第1節獨贏", // 籃
        15 => "Second quarter point spread", // basketball        // 15 => "第2節讓分", // 籃
        16 => "Second quarter over/under", // basketball        // 16 => "第2節大小", // 籃
        17 => "Second quarter odd/even", // basketball        // 17 => "第2節單/雙", // 籃
        18 => "Second quarter winner", // basketball        // 18 => "第2節獨贏", // 籃
        19 => "Third quarter point spread", // basketball        // 19 => "第3節讓分", // 籃
        20 => "Third quarter over/under", // basketball        // 20 => "第3節大小", // 籃
        21 => "Third quarter odd/even", // basketball        // 21 => "第3節單/雙", // 籃
        22 => "Third quarter winner", // basketball        // 22 => "第3節獨贏", // 籃
        23 => "Fourth quarter point spread", // basketball        // 23 => "第4節讓分", // 籃
        24 => "Fourth quarter over/under", // basketball        // 24 => "第4節大小", // 籃
        25 => "Fourth quarter odd/even", // basketball        // 25 => "第4節單/雙", // 籃
        26 => "Fourth quarter winner", // basketball        // 26 => "第4節獨贏", // 籃
        27 => "First 5 innings - Over/under", // baseball       // 27 => "前5局 - 大小", // 棒
        28 => "First 5 innings - Win/draw/lose", // baseball        // 28 => "前5局 - 勝平負", // 棒
        29 => "First 5 innings - Handicap", // baseball        // 29 => "前5局 - 讓球", // 棒
	],
	'sportBetData' => [
		1 => [
			'gameTitle' => ['Full-time winner', 'Full-time handicap', 'Full-time over/under', 'Half-time winner', 'Half-time handicap', 'Half-time over/under', 'All', 'To win', 'Handicap', 'Over/Under'], // ['全場獨贏', '全場讓球', '全場大小', '半場獨贏', '半場讓球', '半場大小', '所有', '獨贏', '讓球', '大小']
			'priorityArr' => ['5', '2', '1', '6', '4', '3'],
			'stage' => ['First half', 'Second half', 'Overtime (or extra time)'], // ['上半場', '下半場', '延長賽']
			'ws' => 'wss://soccer.asgame.net/ws'
		],
		2 => [
			'gameTitle' => ['Full-time winner', 'Full-time handicap', 'Full-time over/under', 'First-half winner', 'First-half handicap', 'First-half over/under', 'All', 'To win', 'Handicap', 'Over/Under'], // ['全場獨贏', '全場讓分', '全場大小', '上半場獨贏', '上半場讓分', '上半場大小', '所有', '獨贏', '讓分', '大小']
			'priorityArr' => ['5', '9', '1', '6', '10', '3'],
			'stage' => ['The first quarter', 'The second quarter', 'The third quarter', 'The fourth quarter', 'Overtime'], // ['第一節', '第二節', '第三節', '第四節', '延長賽']
			'ws' => 'wss://basketball.asgame.net/ws'
		],
		3 => [
			'gameTitle' => ['Full-time winner', 'Full-time handicap', 'Full-time over/under', 'First five innings winner', 'First five innings handicap', 'First five innings over/under', 'All', 'To win', 'Handicap', 'Over/Under'], // ['全場獨贏', '全場讓球', '全場大小', '前五局獨贏', '前五局讓球', '前五局大小', '所有', '獨贏', '讓球', '大小']
			'priorityArr' => ['5', '2', '1', '28', '29', '27'],
			'stage' => ['Full-time winner', 'Full-time handicap', ' Full-time handicap', 'Full-time over/under', 'First five innings winner', 'First five innings over/under', 'All', 'To win', 'Handicap', 'Over/Under'], // ['第一局', '第二局', '第三局', '第四局', '第五局', '第六局', '第七局', '第八局', '第九局', '延長賽']
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
		"firsthalfscore" => "First-half", // "上半場比分",
		"secondhalfscore" => "Second-half ", // "下半場比分",
		"firsthalfscore" => "First-half", // "上半場比分",
		"secondhalfscore" => "Second-half", // "下半場比分",
		"firstquarter" => "First quarter ", // "第一節比分",
		"secondquarter" => "Second quarter", //  "第二節比分",
		"thirdquarter" => "Third quarter", // "第三節比分",
		"fourthquarter" => "Fourth quarter", // "第四節比分",
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