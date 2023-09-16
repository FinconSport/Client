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

            /** 
             * 型態 (integer, decimal, string, text...) -> 
             * 長度 (length) -> 
             * 主鍵 (primary) -> 
             * 遞增 (autoIncrement, id=bigIncrements) ->
             * 可空 (nullable) -> 
             * 唯一 (unique) -> 
             * 索引 (index) ->
             * 外鍵 (foreignId) ->
             * 預設 (default) ->
             * 備註 (comment)
             * */

            //table's default charset & collation
            $table->charset = 'utf8mb4';    //Specify a default character set for the table (MySQL).
            $table->collation = 'utf8mb4_unicode_ci';   //Specify a default collation for the table (MySQL).

            //creating columns
            $table->increments('id')->primary();  //id
            $table->string('account',32)->nullable(false)->unique()->index('admin_account');  //account
            $table->string('password',32)->nullable(false);    //password
            $table->unsignedInteger('permission_id')->nullable(false);    //permission_id
            $table->dateTime('create_time')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));    //create_time
            $table->dateTime('last_login')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));    //last_login
            $table->unsignedSmallInteger('status')->nullable(false)->default(1);    //status

            //foreign key constraints
            $table->foreign('permission_id')->references('id')->on('admin_permission')->constrained()->onUpdate('restrict')->onDelete('restrict');
        });

        //insert data
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
