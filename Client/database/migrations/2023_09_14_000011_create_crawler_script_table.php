<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCrawlerScriptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crawler_script', function (Blueprint $table) {

            //table's default charset & collation
            $table->charset = 'utf8mb4';    //Specify a default character set for the table (MySQL).
            $table->collation = 'utf8mb4_unicode_ci';   //Specify a default collation for the table (MySQL).

            //creating columns
            $table->increments('id')->primary();  //id
            $table->string('crawler_name',128)->nullable(false);    //crawler_name
            $table->string('crawler_url',128)->nullable(false);    //crawler_url
            $table->dateTime('begin_time')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));    //begin_time
            $table->dateTime('last_update')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));    //last_update
            $table->smallInteger('status')->nullable(false)->default(1);    //status

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
        Schema::dropIfExists('crawler_script');
    }
}
