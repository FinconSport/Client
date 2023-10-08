<?php
/****************
 * 
 * 	Order 語系檔
 * 
 */
return [
	// 前端
	"main" => [
		"notice" => "公告",
        "all" => "全部",
        "system" => "系统公告",
        "basketball" => "篮球赛",
        "football" => "足球比赛",
        "baseball" => "棒球比赛",
        "tennis" => "网球比赛",
        "badminton" => "羽毛球",
        "volleyball" => "排球",
        "snooker" => "斯诺克",
        "iceball" => "冰球",
        "no_result" => "沒有相關公告",

	],

	// 後端
    'fixture_cancellation_reasons' => [
        'title:Event Cancelled' => '賽事已取消-:sport_name/:league_name',
        'title:Invalid Event' => '賽事無效-:sport_name/:league_name',
        'title:Wrong League' => '聯盟錯誤-:sport_name/:league_name',
        'title:Participants Swapped' => '選手次序錯誤-:sport_name/:league_name',
        'title:Home/Away Team Corrected' => '主客隊伍錯誤-:sport_name/:league_name',
        'title:Duplication of' => '賽事重複-:sport_name/:league_name',
        'title:Fixture Status Corrected' => '比分資料錯誤-:sport_name/:league_name',
        
        'Event Cancelled' => '賽事已取消將不會開賽: :fixture_start_time/:home_team_name vs. :away_team_name',
        'Invalid Event' => '已取消賽事的資料來源不正確: :fixture_start_time/:home_team_name vs. :away_team_name',
        'Wrong League' => '已取消賽事建立於錯誤的聯盟之下: :fixture_start_time/:home_team_name vs. :away_team_name',
        'Participants Swapped' => '已取消賽事的選手次序錯誤: :fixture_start_time/:home_team_name vs. :away_team_name',
        'Home/Away Team Corrected' => '已取消賽事的主客隊伍錯誤: :fixture_start_time/:home_team_name vs. :away_team_name',
        'Duplication of' => '已取消賽事與另一場ID為:fixture_id的賽事重複: :fixture_start_time/:home_team_name vs. :away_team_name',
        'Fixture Status Corrected' => '已取消賽事收到了不正確的即時比分資料: :fixture_start_time/:home_team_name vs. :away_team_name',
    ]
];