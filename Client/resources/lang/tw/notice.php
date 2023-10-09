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
        'date_time_to_hour' => 'n月j日 H:i',
        'title:Event Cancelled' => '賽事取消-:sport_name/:league_name',
        'title:Invalid Event' => '賽事無效-:sport_name/:league_name',
        'title:Wrong League' => '聯盟錯誤-:sport_name/:league_name',
        'title:Participants Swapped' => '選手次序錯誤-:sport_name/:league_name',
        'title:Home/Away Team Corrected' => '主客隊伍錯誤-:sport_name/:league_name',
        'title:Duplication of' => '賽事重複-:sport_name/:league_name',
        'title:Fixture Status Corrected' => '比分資料錯誤-:sport_name/:league_name',
        
        'Event Cancelled' => '賽事已取消 :fixture_start_time :home_team_name vs. :away_team_name',
        'Invalid Event' => '賽事資料來源不正確故取消 :fixture_start_time :home_team_name vs. :away_team_name',
        'Wrong League' => '賽事聯盟錯誤故取消 :fixture_start_time :home_team_name vs. :away_team_name',
        'Participants Swapped' => '已賽事選手錯序故取消 :fixture_start_time :home_team_name vs. :away_team_name',
        'Home/Away Team Corrected' => '賽事主客隊伍錯誤故取消 :fixture_start_time :home_team_name vs. :away_team_name',
        'Duplication of' => '賽事與:fixture_id重複故取消 :fixture_start_time :home_team_name vs. :away_team_name',
        'Fixture Status Corrected' => '賽事即時比分不正確故取消 :fixture_start_time :home_team_name vs. :away_team_name',
    ]
];