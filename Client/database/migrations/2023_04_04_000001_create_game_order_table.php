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
            $table->integer('id')->length(11)->autoIncrement();
            $table->integer('agent_id')->length(11);
            $table->integer('player_id')->length(11);
            $table->integer('sport_id')->length(11);
            $table->integer('match_id')->length(11);
            $table->integer('type_id')->length(11);
            $table->string('rate')->length(32);
            $table->decimal('bet_amount', 15, 3)->default(0);
            $table->decimal('result_amount', 15, 3)->default(0);
            $table->string('comp')->length(32);
            $table->string('home')->length(32);
            $table->string('away')->length(32);
            $table->string('short_comp')->length(32);
            $table->string('short_home')->length(32);
            $table->string('short_away')->length(32);
            $table->string('issue_num')->length(32);
            $table->integer('match_time')->length(11);
            $table->integer('home_score')->length(11)->default(0);
            $table->integer('away_score')->length(11)->default(0);
            $table->integer('half_home_score')->length(11)->default(0);
            $table->integer('half_away_score')->length(11)->default(0);
            $table->tinyInteger('is_better_rate')->length(4)->default(1);
            $table->tinyInteger('status')->length(4)->default(1);
            $table->dateTime('create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('result_time')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_order');
    }
}
