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
        'Title:Event Cancelled' => '賽事已取消',
        'Title:Invalid Event' => '賽事無效',
        'Title:Wrong League' => '聯盟錯誤',
        'Title:Participants Swapped' => '選手次序錯誤',
        'Title:Home/Away Team Corrected' => '主客隊伍錯誤',
        'Title:Duplication of' => '賽事重複',
        'Title:Fixture Status Corrected' => '比分資料錯誤',
        
        'Event Cancelled' => '賽事已取消將不會開賽',
        'Invalid Event' => '已取消賽事的資料來源不正確導致',
        'Wrong League' => '已取消賽事建立於錯誤的聯盟之下',
        'Participants Swapped' => '已取消賽事的選手次序錯誤',
        'Home/Away Team Corrected' => '已取消賽事的主客隊伍錯誤',
        'Duplication of' => '已取消賽事與另一場ID為:fixture_id的賽事重複',
        'Fixture Status Corrected' => '已取消賽事收到了不正確的即時比分資料',
    ]
];