<?php
/****************
 * 
 * 	通用類 語系檔
 * 
 */
return [
	// 前端
	'order'=> [6046, 48242, 154914, 35232, 131506],
	"left_menu" => [
		"sport_bet" => "Match Betting", // "體育投注"
		"m_bet" => "Parlay Betting", // "串關投注"
		"record" => "Betting Record", // "注單紀錄"
		"all" => "All",
		"settled" => "Settled",
		"unsettled" => "Unsettled",
		"match" => "Game Results", // "賽事結果"
		"rule" => "Betting Rules", // "競猜規則"
		"logs" => "Transaction Record", // "帳變紀錄"
		"calculator" => "Parlay Calculation", // "串關算法"
		"notice" => "Annoucement", // "公告"
		"logout" => "Log out", // "登出"
	],
	'sport_menu' => [
		'today' => 'Today', // "今日"
		'living' => 'Live Betting', // "滾球"
		'early' => 'Early Odds', // "早盤"
	],
	"search_area" => [
		'sport' => 'Sports Types', // "體育種類"
		'order_id' => 'Order ID', // "訂單編號"
		'logs_id' => 'Transaction ID', // "交易編號"
		'logsType' => 'Type', // "類型"
		'gamestatus' => 'Game Status', // "比賽狀態"
		'status' => 'Status', // "狀態"
		'series' => 'Tournament Name', // "聯賽名稱"
		'start_time' => 'Start Time', // "開始時間"
		'end_time' => 'End Time', // "結束時間"
		'search' => 'Search', // "搜尋"
		'total' => 'Total', // "共"
		'game' => '', // "場"
		'all' => 'All', // "全部"
		"last_month" => "Last Month",  //  "上月",
		"last_week" => "Last Week",  //  "上週",
		"yesterday" => "Yesterday",  //  "昨日",
		"today" => "Today",  //  "今日",
		"this_week" => "This Week",  //  "本週",
		"this_month" => "This Month",  //  "本月",
	],
	'js' => [
		'loginFirst' => 'Please log in first!', // "請先登入!"
		'sun' => 'Sun', // "日"
		'mon' => 'Mon', // "一"
		'tue' => 'Tue', // "二"
		'wed' => 'Wed', // "三"
		'thu' => 'Thu', // "四"
		'fri' => 'Fri', // "五"
		'sat' => 'Sat', // "六"
		'jan' => 'January', // "一月"
		'feb' => 'February', // "二月"
		'mar' => 'March',  // "三月"
		'apr' => 'April', // "四月"
		'may' => 'May', // "五月"
		'jun' => 'June', // "六月"
		'jul' => 'July', // "七月"
		'aug' => 'August', // "八月"
		'sep' => 'September', // "九月"
		'oct' => 'October', // "十月"
		'nov' => 'November', // "十一月"
		'dec' => 'December', // "十二月"
		'today' => 'Today', // "今天"
	],
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
		131506 => [
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
	],
	'priorityArr' => [
		'allwin' => [1, 2, 9, 101, 102, 109, 113, 117, 121, 201, 202, 301],
		'hcap' => [3, 4, 103, 104, 110, 114, 118, 122, 203 , 204, 302],
		'oddeven' => [7, 107, 108, 112, 116, 120, 124, 207, 208, 304],
		'size' => [5, 6, 205, 206, 105, 106, 111, 115, 119, 123, 303],
		'bd' => [8]
	],

	// 後端
	'ajax.ERROR_login_01' => 'Login verification failed, please retry', // "登入驗證失敗,請重試"
];