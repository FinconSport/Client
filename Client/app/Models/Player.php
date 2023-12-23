<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Player extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "player";

}
