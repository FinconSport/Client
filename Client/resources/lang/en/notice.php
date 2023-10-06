<?php
/****************
 * 
 * 	Order 語系檔
 * 
 */
return [
	// 前端
	"main" => [
		"notice" => "Announcement",  // "公告",
        "all" => "All",  // "全部",
        "system" => "System Announcement",  // "系统公告",
        "basketball" => "Basketball Game",  // "篮球赛",
        "football" => "Football Game",  // "足球比赛",
        "baseball" => "Baseball Game",  // "棒球比赛",
        "tennis" => "Tennis Game",  // "网球比赛",
        "badminton" => "Badminton",  // "羽毛球",
        "volleyball" => "Volleyball",  // "排球",
        "snooker" => "Snooker",  // "斯诺克",
        "iceball" =>  "Ice Hockey",  // "冰球",
        "no_result" =>  "No Relevant Announcements",  // "沒有相關公告",
	],

	// 後端

    // 賽事取消原因. 用於DB::lsport_notice
    'fixture_cancellation_reasons' => [
        'title:Event Cancelled' => 'Event cancelled',
        'title:Invalid Event' => 'Event invalid',
        'title:Wrong League' => 'Event wrong league',
        'title:Participants Swapped' => 'Participants swapped',
        'title:Home/Away Team Corrected' => 'Home/away team incorrect',
        'title:Duplication of' => 'Event duplicated',
        'title:Fixture Status Corrected' => 'Event wrong status',

        'Event Cancelled' => 'The event was canceled and will not take place',
        'Invalid Event' => 'The canceled event was created according to incorrect metadata',
        'Wrong League' => 'The event was created under the wrong league',
        'Participants Swapped' => 'The canceled event was created with incorrect order of participants',
        'Home/Away Team Corrected' => '	The canceled event was created with an incorrect home/away participant',
        'Duplication of' => 'The cancelled event duplicates with the nother event with ID :fixture_id',
        'Fixture Status Corrected' => 'The canceled event received an incorrect Livescore update',
    ]
];