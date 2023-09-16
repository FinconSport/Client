<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerUser extends Model
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "seller_user";

}
