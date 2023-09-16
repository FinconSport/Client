<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerBalanceLogs extends Model
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "player_balance_logs";

}

