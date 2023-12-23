<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->integer('id')->length(11)->autoIncrement();
            $table->string('account')->length(32)->unique();
            $table->string('password')->length(32);
            $table->integer('permission_id')->length(11);
            $table->dateTime('create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('last_login')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->tinyInteger('status')->length(4)->default(1);
        });

        
		$data = [
			'account' => 'admin',
			'password' => '827ccb0eea8a706c4c34a16891f84e7b',
			'permission_id' => '1'
		];
        DB::table('admin')->insert($data);
    }

    /**
     * Reverse the migrations.
     * 
     * 
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin');
    }
}
