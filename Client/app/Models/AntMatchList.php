<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntMatchList extends Model
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "ant_match_list";

}
