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
            $table->integer('id')->length(11)->autoIncrement();
            $table->integer('agent_id')->length(11);
            $table->integer('player_id')->length(11);
            $table->string('player')->length(32);
            $table->tinyInteger('currency_type')->length(4)->default(0);
            $table->string('type')->length(32);
            $table->decimal('change_balance', 15, 3)->default(0);
            $table->decimal('before_balance', 15, 3)->default(0);
            $table->decimal('after_balance', 15, 3)->default(0);
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
        Schema::dropIfExists('player_balance_logs');
    }
}
