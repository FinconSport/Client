<?php
/****************
 * 
 * 	Index 語系檔
 * 
 */
return [
    "scoreBoard" => [
      'gameTitle' => [
        154914 => ['Total', '1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th'],
        6046 => ['Total', 'First Half', 'Second Half'],
        48242 => ['Total', 'Q1', 'Q2', 'Q3', 'Q4'],
        35232 => ['Total', '1st', '2nd', '3rd'],
      ],
      'upperStage'=> 'Top',
      'lowerStage'=> 'Bottom',
      "fullTimeScore" => "Total",
        "q1" => "Q1",
        "q2" => "Q2",
        "q3" => "Q3",
        "q4" => "Q4",
        "gameOne" => "1",
        "gameTwo" => "2",
        "gameThree" => "3",
        "gameFour" => "4",
        "gameFive" => "5",
        "gameSix" => "6",
        "gameSeven" => "7",
        "gameEight" => "8",
        "gameNine" => "9",
        "gameTen" => "10",
        "gameEleven" => "11",
        "gameTwelve" => "12",
        "gamesOn" => "games on",
        "totalScore" => "Total Score",
        'ready' => 'Ready To Start'
    ],
    "index" => [
        "th" => "th",
        "dateTimezone" => "en-US",
        "all" => "All",
        "hot" => "Hot",
        "tie" => "Tie",
        "correct_score" => "Correct Score",
        'full' => 'Full Game',
		    'half' => 'Half Game',
		    '1qtr' => '1st',
        '2qtr' => '2nd',
        '3qtr' => '3rd',
        '4qtr' => '4th',
    ],
    "game_priority" => [
        "154914" => [// Baseball
          "1" => "Full Game Moneyline", 
          "3" => "Full Game Point Spread",
          "5" => "Full Game Totals", 
          "7" => "Full Game Odd/Even", 
          "2" => "First Five Innings Moneyline", 
          "4" => "First Five Innings Point Spread", 
          "6" => "First Five Innings Totals", 
          "8" => "Full Game Correct Score", 
          "9" => "First Five Innings Moneyline", 
        ],
        "48242" => [ // Basketball
          "101" => "Full Game Moneyline", 
          "102" => "First Half Moneyline",
          "103" => "Full Game Point Spread", 
          "104" => "First Half Point Spread", 
          "105" => "Full Game Totals", 
          "106" => "First Half Totals", 
          "107" => "Full Game Odd/Even", 
          "108" => "First Half Odd/Even",
          "109" => "1st Quarter Moneyline", 
          "110" => "1st Quarter Point Spread", 
          "111" => "1st Quarter Totals", 
          "112" => "1st Quarter Odd/Even",
          "113" => "2nd Quarter Moneyline", 
          "114" => "2nd Quarter Point Spread",
          "115" => "2nd Quarter Totals", 
          "116" => "2nd Quarter Odd/Even", 
          "117" => "3rd Quarter Moneyline", 
          "118" => "3rd Quarter Point Spread",
          "119" => "3rd Quarter Totals",
          "120" => "3rd Quarter Odd/Even",
          "121" => "4th Quarter Moneyline",
          "122" => "4th Quarter Point Spread",
          "123" => "4th Quarter Totals",
          "124" => "4th Quarter Odd/Even",
        ],
        "6046" => [ // Football
          "201" => "Full Game Moneyline", 
          "202" => "First Half Moneyline",
          "203" => "Full Game Point Spread", 
          "204" => "First Half Point Spread", 
          "205" => "Full Game Totals", 
          "206" => "First Half Totals", 
          "207" => "Half match Odd/Even",
          "208" => "Whole match Odd/Even",
        ],
        "35232" => [// Hockey
          "301" => "Full Game Moneyline", 
          "302" => "Full Game Point Spread",
          "303" => "Full Game Totals", 
          "304" => "Full Game Odd/Even", 
          "305" => "First Half Moneyline",
          "306" => "First Half Point Spread",
          "307" => "First Half Totals",
          "308" => "First Half Odd/Even",
        ],
    ],
    'catePriority' => [
      'full' => [1, 3, 5, 7, 101, 103, 105, 107, 201, 203, 205, 208, 301, 302, 303, 304, 401, 403, 405, 407],
      'half' => [2, 4, 6, 8, 9, 102, 104, 106, 108, 202, 204, 206, 207, 305, 306, 307, 308, 402, 404, 406, 408],
      'single' => [
        48242 => [
          1 => [109, 110, 111, 112],
          2 => [113, 114, 115, 116],
          3 => [117, 118, 119, 120],
          4 => [121, 122, 123, 124]
        ]
      ]
    ]
];
