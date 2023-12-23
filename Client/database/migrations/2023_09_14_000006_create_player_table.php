<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePlayerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player', function (Blueprint $table) {

            //table's default charset & collation
            $table->charset = 'utf8mb4';    //Specify a default character set for the table (MySQL).
            $table->collation = 'utf8mb4_unicode_ci';   //Specify a default collation for the table (MySQL).

            //creating columns
            $table->increments('id')->primary();  //id
            $table->unsignedInteger('agent_id')->nullable(false)->index('player_agent_id');    //agent_id
            $table->string('account',32)->nullable(false)->unique()->index('player_account');    //account
            $table->string('token',32)->nullable(false);    //token
            $table->unsignedTinyInteger('currency_type')->nullable(false)->default(0);    //currency_type
            $table->unsignedTinyInteger('merchant_type')->nullable(false)->default(0);    //merchant_type
            $table->decimal('balance', 15, 3)->nullable(false)->default(0.0);    //balance
            $table->text('rate_data')->nullable(true);    //rate_data
            $table->dateTime('create_time')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));    //create_time
            $table->dateTime('last_update')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));    //last_update
            $table->smallInteger('status')->nullable(false)->default(1);    //status

            //foreign key constraints
            $table->foreign('agent_id')->references('id')->on('agent')->constrained()->onUpdate('restrict')->onDelete('restrict');

        });

        //insert data
        $data = [
            ['agent_id' => '1','account' => 'test01','token' => '827ccb0eea8a706c4c34a16891f84e7b','currency_type' => '1','merchant_type' => '0','balance' => '99999'],
            ['agent_id' => '1','account' => 'test02','token' => '827ccb0eea8a706c4c34a16891f84e7b','currency_type' => '1','merchant_type' => '0','balance' => '10000'],
        ];
        DB::table('player')->insert($data);
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
        Schema::dropIfExists('player');
    }
}
