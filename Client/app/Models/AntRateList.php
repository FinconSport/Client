<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntRateList extends Model
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "ant_rate_list";
	
	protected $fillable = ['auto_update_switch'];

}
