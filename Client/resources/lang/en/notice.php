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
    'fixture_cancellation_reasons' => [
        'Event Cancelled' => 'The event was canceled and will not take place',
        'Invalid Event' => 'The canceled event was created according to incorrect metadata',
        'Wrong League' => 'The event was created under the wrong league',
        'Participants Swapped' => 'The canceled event was created with incorrect order of participants',
        'Home/Away Team Corrected' => '	The canceled event was created with an incorrect home/away participant',
        'Duplication of' => 'There are two instances of the same fixture, and we have canceled one of them. Refer to the Fixture ID in the :fixture_id',
        'Fixture Status Corrected' => 'The canceled event received an incorrect Livescore update',
    ]
];