<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LsportNotice extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "lsport_notice";

}
