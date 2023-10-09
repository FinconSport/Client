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
        'date_time_to_hour' => 'd M H:i',
        'title:Event Cancelled' => 'Event cancelled-:sport_name/:league_name',
        'title:Invalid Event' => 'Event invalid-:sport_name/:league_name',
        'title:Wrong League' => 'Event wrong league-:sport_name/:league_name',
        'title:Participants Swapped' => 'Participants swapped-:sport_name/:league_name',
        'title:Home/Away Team Corrected' => 'Home/away team incorrect-:sport_name/:league_name',
        'title:Duplication of' => 'Event duplicated-:sport_name/:league_name',
        'title:Fixture Status Corrected' => 'Event wrong status-:sport_name/:league_name',

        'Event Cancelled' => 'The event was cancelled: :sport_name/:league_name/:fixture_start_time/:home_team_name vs. :away_team_name',
        'Invalid Event' => 'The canceled event was created according to incorrect metadata: :sport_name/:league_name/:fixture_start_time/:home_team_name vs. :away_team_name',
        'Wrong League' => 'The event was created under the wrong league: :sport_name/:league_name/:fixture_start_time/:home_team_name vs. :away_team_name',
        'Participants Swapped' => 'The canceled event was created with incorrect order of participants: :sport_name/:league_name/:fixture_start_time/:home_team_name vs. :away_team_name',
        'Home/Away Team Corrected' => '	The canceled event was created with an incorrect home/away participant: :sport_name/:league_name/:fixture_start_time/:home_team_name vs. :away_team_name',
        'Duplication of' => 'The cancelled event duplicates with the nother event with ID :fixture_id: :sport_name/:league_name/:fixture_start_time/:home_team_name vs. :away_team_name',
        'Fixture Status Corrected' => 'The canceled event received an incorrect Livescore update: :sport_name/:league_name/:fixture_start_time/:home_team_name vs. :away_team_name',
    ]
];