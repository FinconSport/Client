<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateNamiRateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nami_rate', function (Blueprint $table) {
            $table->integer('id')->length(11)->autoIncrement();
            $table->integer('sport_id')->length(11);
            $table->string('comp')->length(32);
            $table->string('home')->length(32);
            $table->string('away')->length(32);
            $table->string('short_comp')->length(32);
            $table->string('short_home')->length(32);
            $table->string('short_away')->length(32);
            $table->integer('is_same')->length(11);
            $table->text('rate_data');
            $table->dateTime('create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('issue_num')->length(32);
            $table->integer('match_time')->length(11);
            $table->tinyInteger('sell_status')->length(4)->default(1);
            $table->tinyInteger('status')->length(1)->default(1);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nami_rate');
    }
}
