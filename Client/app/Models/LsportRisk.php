<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LsportRisk extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "lsport_risk";

}
