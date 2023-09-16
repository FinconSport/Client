<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntGameList extends Model
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "ant_game_list";

}
