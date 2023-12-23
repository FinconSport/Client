<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateGameResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_result', function (Blueprint $table) {
            $table->integer('id')->length(11)->autoIncrement();
            $table->integer('sport_id')->length(11);
            $table->string('comp')->length(32);
            $table->string('home')->length(32);
            $table->string('away')->length(32);
            $table->string('short_comp')->length(32);
            $table->string('short_home')->length(32);
            $table->string('short_away')->length(32);
            $table->string('issue_num')->length(32);
            $table->integer('match_time')->length(11);
            $table->integer('home_score')->length(11);
            $table->integer('away_score')->length(11);
            $table->integer('half_home_score')->length(11);
            $table->integer('half_away_score')->length(11);
            $table->text('rate_data');
            $table->tinyInteger('sell_status')->length(4)->default(1);
            $table->tinyInteger('status')->length(4)->default(1);
            $table->dateTime('last_update')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_result');
    }
}
