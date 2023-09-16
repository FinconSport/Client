<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateGameOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_order', function (Blueprint $table) {

            //table's default charset & collation
            $table->charset = 'utf8mb4';    //Specify a default character set for the table (MySQL).
            $table->collation = 'utf8mb4_unicode_ci';   //Specify a default collation for the table (MySQL).

            //creating columns
            /**
             * 注意: 欄位 unsigned 宣告可能之衍生問題
             * 雖然數值欄位宣告為 unsigned 後儲存範圍可增加一倍
             * 但不是用於可能會有負數出現的欄位
             */
            $table->increments('id')->primary();  //id
            $table->integer('m_id')->nullable(true)->default(null);    //m_id
            $table->unsignedTinyInteger('m_order')->nullable(false)->default(0);    //m_order
            $table->unsignedInteger('agent_id')->nullable(false)->index('game_order_agent_id');    //agent_id
            $table->string('agent_name',32)->nullable(false);    //agent_name
            $table->unsignedInteger('player_id')->nullable(false)->index('game_order_player_id');    //player_id
            $table->string('player_name',32)->nullable(false);    //player_name
            $table->unsignedTinyInteger('currency_type')->nullable(false);    //currency_type
            $table->unsignedInteger('series_id')->nullable(false);    //series_id
            $table->string('series_name',64)->nullable(false);    //series_name
            $table->unsignedInteger('game_id')->nullable(false);    //game_id
            $table->iunsignedIntegernteger('match_id')->nullable(false);    //match_id
            $table->unsignedInteger('type_id')->nullable(false);    //type_id
            $table->unsignedInteger('type_item_id')->nullable(false);    //type_item_id
            $table->string('type_name',64)->nullable(false);    //type_name
            $table->string('type_item_name',64)->nullable(false);    //type_item_name
            $table->unsignedInteger('home_team_id')->nullable(false);    //home_team_id
            $table->string('home_team_name',64)->nullable(false);    //home_team_name
            $table->unsignedInteger('away_team_id')->nullable(false);    //away_team_id
            $table->string('away_team_name',64)->nullable(false);    //away_team_name
            $table->string('home_team_score',4)->nullable(true)->default(null);    //home_team_score
            $table->string('away_team_score',4)->nullable(true)->default(null);    //away_team_score
            $table->unsignedSmallInteger('type_priority')->nullable(false);    //type_priority
            $table->decimal('bet_amount', 15, 3)->nullable(false);    //bet_amount
            $table->decimal('bet_rate', 15, 5)->nullable(false);    //bet_rate
            $table->decimal('player_rate', 15, 5)->nullable(false);    //player_rate
            $table->smallInteger('better_rate')->nullable(false);    //better_rate
            $table->text('result_data')->nullable(true);    //result_data
            $table->decimal('result_amount', 15, 3)->nullable(false)->default(0.0);    //result_amount
            $table->decimal('result_percent', 15, 3)->nullable(false)->default(0.0);    //result_percent
            $table->dateTime('create_time')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));    //create_time
            $table->dateTime('approval_time')->nullable(true)->default(null);    //approval_time
            $table->dateTime('result_time')->nullable(true)->default(null);    //result_time
            $table->unsignedTinyInteger('is_result')->nullable(false)->default(0);    //is_result
            $table->unsignedTinyInteger('is_retry')->nullable(false)->default(0);    //is_retry
            $table->unsigneSmallInteger('try_count')->nullable(false)->default(0);    //try_count
            $table->unsigneSmallInteger('status')->nullable(false);    //status

            //foreign key constraints
            /*
             * for efficiency's concern, add NO foreign key contraint
             */
            //$table->foreign('agent_id')->references('id')->on('agent')->constrained()->onUpdate('restrict')->onDelete('restrict');
            //$table->foreign('player_id')->references('id')->on('player')->constrained()->onUpdate('restrict')->onDelete('restrict');

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
        Schema::dropIfExists('game_order');
    }
}
