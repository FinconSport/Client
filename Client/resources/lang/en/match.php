<?php
/****************
 * 
 * 	Match 語系檔
 * 
 */
return [
	// 前端
	"main" => [
		"score" => "Score", // "分數",
		"nomoredata" => "No more data available", // "沒有更多資料了",
		"nogame" => "No matches currently", // "目前沒有賽事",
		"date" => "Date", // "日期",
		"series" => "Tournament", // "聯賽",
		"homeaway" => "Opposing teams", // "對戰隊伍",
		"overtime" => "Overtime", // "加時",
		"firstfivematch" => "1st 5 innings", // "前五局",
		"wholematchscore" => "Total score", // "總分",
		"hitpoint" => "Hits", // "安打",
	],
	"game"=> [
		"soccer" => "Soccer", // "足球",
		"basketball" => "Basketball", // "籃球",
		"baseball" => "Baseball", // "棒球",
		"iceball" => "Ice Hockey", // "冰球",
		"tennis" => "Tennis", // "網球",
		"football" => "American Football", // "美式足球",
		"snooker" => "Snooker", // "斯諾克",
		"tabletennis" => "Table Tennis", // "乒乓球",
		"volleyball" => "Volleyball", // "排球",
	],
	"filter" => [
		"sport" => "Sports Type", // "體育種類",
		"series" => "Tournament Name", // "聯賽名稱",
		"start_time" => "Start Time", // "開始時間,
		"end_time" => "End Time", // "結束時間",
	],
	"basketball" => [
		"fulltimescore" => "Full Game", // "全場比分",
		"firsthalfscore" => "1st Half", // "上半場比分",
		"secondhalfscore" => "2nd Half", // "下半場比分",
		"firstquarter" => "1st Quarter", // "第一節比分",
		"secondquarter" => "2nd Quarter", // "第二節比分",
		"thirdquarter" => "3rd Quarter", //" 第三節比分",
		"fourthquarter" => "4th Quarter", // "第四節比分",
		"twopoints" => "Two-Pointers", // "兩分",
		"threepoints" => "Three-Pointers", // "三分",
		"penalty" => "Free Throws", // "罰球",
		"freethrowpercentage" => "Free Throw Percentage", // "罰球命中率,
		"numberoffreethrows" => "Free Throw Attempts", // "罰球次數,
		"totalnumberoffouls" => "Total Fouls", // "總犯規次數",
		"foulsfirstquarter" => "1st Quarter Fouls", // "第一節犯規次數",
		"foulssecondquarter" => "2nd Quarter Fouls", // "第二節犯規次數",
		"foulsthirdquarter" => "3rd Quarter Fouls", // "第三節犯規次數",
		"foulsfourthquarter" => "4th Quarter Fouls", // "第四節犯規次數",
		"overtimefouls" => "Overtime Fouls", // "加時賽犯規次數",
		"foulsfirsthalf" => "1st Half Fouls", // "上半場犯規次數",
		"foulssecondhalf" => "2nd Half Fouls", // "下半場犯規次數",
		"totalnumberofpauses" => "Total Timeouts", // "總暫停次數",
		"timeoutsfirstquarter" => "1st Quarter Timeouts", // "第一節暫停次數",
		"timeoutssecondquarter" => "2nd Quarter Timeouts", // "第二節暫停次數",
		"timeoutsthirdquarter" => "3rd Quarter Timeouts", // "第三節暫停次數",
		"timeoutsfourthquarter" => "4th Quarter Timeouts", // "第四節暫停次數",
		"overtimetimeouts" => "Overtime Timeouts", // "加時賽暫停次數",
	],
	"baseball" => [
		"fulltimescore" => "Full Game Score", // "全場比分",
		"firstround" => "1st Inning", // "第一局,
		"secondgame" => "2nd Inning",  //"第二局"
		"thirdinning" => "3rd Inning", //"第三局"
		"fourthinning" => "4th Inning",  // "第四局"
		"fifthinning" => "5th Inning",  // "第五局"
		"sixthinning" => "6th Inning",  // "第六局"
		"seventhinning" => "7th Inning",  // "第七局"
		"eighthinning" => "8th Inning", // "第八局"
		"ninthinning" => "9th Inning",  // "第九局"
		"tenthinning" => "10th Inning", // "第十局"
		"eleventhinning" => "11th Inning", // "第十一局"
		"twelfthinning" => "12th Inning", //"第十二局"
		"overtime"  => "Overtime", // "加時"
		"hitscore" => "Hits Score",  // "安打比分"
	],
	"football" => [
		"fulltimescore" => "Full Game Score", // "全場比分",
		"firsthalfscore" => "1st Half Score", // "上半場比分",
		"secondhalfscore" => "2nd Half Score", // "下半場比分",
		"cornerscore" => "Corner Kicks Score", // "角球比分",
		"freekickscore" => "Free Kicks Score", // "任意球比分",
		"overtimescore" => "Overtime Score", // "加時賽比分",
		"scoreofdangerousoffenses" => "Dangerous Offenses Score", // "危險進攻次數比分",
		"penaltyscore" => "Penalty Kicks Score", // "點球比分",
		"redcardscore" => "Red Cards Score", // "紅牌比分",
		"yellowcardscore" => "Yellow Cards Score", // "黃牌比分",
		"scoreofmissedshots" => "Shots Off Target Score", // "射偏次數比分",
		"scoreofshotsontarget" => "Shots On Target Score", // "射正次數比分",
		"numberofattacks" => "Total Attacks", // "進攻次數",
	],
	'matchTitle' => [
		'commonTitle' => ['Date', 'League', 'Match'],
		6046 => ['Total', '1st Half', '2nd Half', 'Overtime'],
		48242 => ['Total', '1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter', 'Overtime'],
		154914 => ['Total', '1st Set', '2nd Set', '3rd Set', '4th Set', '5th Set', '6th Set', '7th Set', '8th Set', '9th Set', 'Overtime']
	]
	// 後端
];