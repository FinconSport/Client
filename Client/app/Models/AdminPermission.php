<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminPermission extends Model
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "admin_permission";

}

