<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerOnline extends Model
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "player_online";

}

