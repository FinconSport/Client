<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateGameRiskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_risk', function (Blueprint $table) {

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
            $table->unsignedInteger('game_id')->nullable(false);    //game_id
            $table->unsignedInteger('series_id')->nullable(false);    //series_id
            $table->unsignedInteger('match_id')->nullable(false);    //match_id
            $table->unsignedSmallInteger('game_priority')->nullable(false);    //game_priority
            $table->string('data',256)->nullable(false);    //data
            $table->unsignedInteger('updated_at')->nullable(false);    //updated_at

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
        Schema::dropIfExists('game_risk');
    }
}
