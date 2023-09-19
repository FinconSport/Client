<?php
/****************
 * 
 * 	Calculator 語系檔
 * 
 */
return [
	// 前端
	"calculator" => [
		"calculator" => "Parlay Calculator", // "串關計算機",
		"index" => "Code", // "編號",
		"rate" => "Betting Odds", // "賠率",
		"condition" => "Handicap", // "分盤",
		"condition_1" => "Win * Odds", // "贏 * 賠率",
		"condition_2" => "Lose * 0", // "輸 * 0",
		"condition_3" => "Win Half * (1 + (Odds - 1) / 2)", // "贏半 * (1 + (賠率 - 1) / 2)",
		"condition_4" => "Lose Half * 0.5", // "輸半 * 0.5",
		"condition_5" => "Push (Return of Stake)",// "走水 (退回本金)",
		"condition_6" => "Void (Return of Stake)", // "取消 (退回本金)",
		"process" => "Calculation Process : ", // "計算過程 : ",
		"betmoney" => "Betting Amount", // "投注金額",
		"winmoney" => "Potential Winnings", //"可贏金額",
		"clear" => "Clear To Zero", // "清零",
		"calculate" => "Calculate", //"計算",
		"noinputmoney" => "Please Enter The Betting Amount", // "請輸入投注金額",
	],
	// 後端
];