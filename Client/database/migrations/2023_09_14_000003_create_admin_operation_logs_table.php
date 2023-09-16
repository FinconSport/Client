<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAdminOperationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_operation_logs', function (Blueprint $table) {

            //table's default charset & collation
            $table->charset = 'utf8mb4';    //Specify a default character set for the table (MySQL).
            $table->collation = 'utf8mb4_unicode_ci';   //Specify a default collation for the table (MySQL).

            //creating columns
            $table->increments('id')->primary();  //id
            $table->string('action',32)->nullable(false);  //action
            $table->string('account',32)->nullable(false);  //account
            $table->text('before_data')->nullable(true);    //before_data
            $table->text('after_data')->nullable(true);    //after_data
            $table->dateTime('create_time')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));    //create_time
            $table->unsignedSmallInteger('status')->nullable(false)->default(1);    //status

            //foreign key constraints

        });

        //insert data
		// $data = [
		// 	'col' => 'val', ...
		// ];
        // DB::table('admin')->insert($data);
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
        Schema::dropIfExists('admin_operation_logs');
    }
}
