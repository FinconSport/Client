<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntNoticeList extends Model
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "ant_notice_list";

}
