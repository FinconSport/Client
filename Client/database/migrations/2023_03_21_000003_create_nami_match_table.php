<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateNamiMatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nami_match', function (Blueprint $table) {
            $table->integer('id')->length(11)->autoIncrement();
            $table->integer('sport_id')->length(11);
            $table->integer('match_id')->length(11);
            $table->integer('lottery_type')->length(11);
            $table->string('issue')->length(32);
            $table->string('issue_num')->length(32);
            $table->string('home_name')->length(32);
            $table->string('away_name')->length(32);
            $table->integer('is_same')->length(11);
            $table->tinyInteger('status')->length(1)->default(1);
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
        Schema::dropIfExists('nami_match');
    }
}
