<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePlayerOnlineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_online', function (Blueprint $table) {

            //table's default charset & collation
            $table->charset = 'utf8mb4';    //Specify a default character set for the table (MySQL).
            $table->collation = 'utf8mb4_unicode_ci';   //Specify a default collation for the table (MySQL).

            //creating columns
            $table->increments('id')->primary();  //id
            //$table->integer('agent_id')->nullable(false)->index('player_balance_logs_agent_id');    //agent_id
            //$table->integer('player_id')->nullable(false)->index('player_balance_logs_player_id');    //player_id
            $table->unsignedInteger('agent_id')->nullable(false);    //agent_id
            $table->unsignedInteger('player_id')->nullable(false);    //player_id
            $table->string('token',32)->nullable(false);    //token
            $table->dateTime('create_time')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));    //create_time
            $table->dateTime('last_update')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));    //last_update
            $table->smallInteger('status')->nullable(false)->default(1);    //status

            //foreign key constraints
            $table->foreign('agent_id')->references('id')->on('agent')->constrained()->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('player_id')->references('id')->on('player')->constrained()->onUpdate('restrict')->onDelete('restrict');

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
        Schema::dropIfExists('player_online');
    }
}
