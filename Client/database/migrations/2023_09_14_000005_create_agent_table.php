<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAgentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent', function (Blueprint $table) {

            //table's default charset & collation
            $table->charset = 'utf8mb4';    //Specify a default character set for the table (MySQL).
            $table->collation = 'utf8mb4_unicode_ci';   //Specify a default collation for the table (MySQL).

            //creating columns
            $table->increments('id')->primary();  //id
            $table->string('account',32)->nullable(false)->unique()->index('agent_account');  //account
            $table->string('email',64)->nullable(false);  //email
            $table->string('password',32)->nullable(false);    //password
            $table->string('prefix',32)->nullable(false);    //prefix
            $table->string('api_lang',32)->nullable(false)->default('tw');    //api_lang
            $table->unsignedTinyInteger('currency_type')->nullable(false)->default(1);    //currency_type
            $table->unsignedTinyInteger('merchant_type')->nullable(false)->default(1);    //merchant_type
            $table->string('token',32)->nullable(false);    //token
            $table->unsignedTinyInteger('is_beta')->nullable(false)->default(1);    //is_beta
            $table->text('limit_data')->nullable(true);    //limit_data
            $table->text('white_list')->nullable(false);    //white_list
            $table->decimal('balance', 15, 3)->nullable(false)->default(0.0);    //balance
            $table->dateTime('create_time')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));    //create_time
            $table->dateTime('last_login')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));    //last_login
            $table->smallInteger('status')->nullable(false)->default(1);    //status

            //foreign key constraints

        });

        //insert data
        $data = [
            'account' => 'admin',
            'email' => 'admin@ft-game.com',
            'password' => '827ccb0eea8a706c4c34a16891f84e7b',
            'prefix' => 'beta',
            'currency_type' => 1,
            'merchant_type' => 0,
            'token' => '827ccb0eea8a706c4c34a16891f84e7b',
            'is_beta' => 1,
            'white_list' => '[]',
            'create_time' => '2023-03-16 09:57:23',
            'last_login' => '2023-03-16 09:57:23',
            'status' => 1
        ];
        DB::table('agent')->insert($data);
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
        Schema::dropIfExists('agent');
    }
}
