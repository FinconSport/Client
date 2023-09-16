<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePlayerBalanceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_balance_logs', function (Blueprint $table) {

            //table's default charset & collation
            $table->charset = 'utf8mb4';    //Specify a default character set for the table (MySQL).
            $table->collation = 'utf8mb4_unicode_ci';   //Specify a default collation for the table (MySQL).

            //creating columns
            $table->increments('id')->primary();  //id
            $table->unsignedInteger('agent_id')->nullable(false)->index('player_balance_logs_agent_id');    //agent_id
            $table->unsignedInteger('player_id')->nullable(false)->index('player_balance_logs_player_id');    //player_id
            $table->string('player',32)->nullable(false);    //player
            $table->unsignedTinyInteger('currency_type')->nullable(false)->default(0);    //currency_type
            $table->string('type',32)->nullable(false);    //type
            $table->decimal('change_balance', 15, 3)->nullable(false);    //change_balance
            $table->decimal('before_balance', 15, 3)->nullable(false);    //before_balance
            $table->decimal('after_balance', 15, 3)->nullable(false);    //after_balance
            $table->dateTime('create_time')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));    //create_time

            //foreign key constraints
            $table->foreign('agent_id')->references('id')->on('agent')->constrained()->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('player_id')->references('id')->on('player')->constrained()->onUpdate('restrict')->onDelete('restrict');
            //$table->foreign('player')->references('id')->on('agent')->constrained()->onUpdate('restrict')->onDelete('restrict');

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
        Schema::dropIfExists('player_balance_logs');
    }
}
