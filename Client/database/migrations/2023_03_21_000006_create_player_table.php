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
            $table->integer('id')->length(11)->autoIncrement();
            $table->integer('agent_id')->length(11);
            $table->string('account')->length(32);
            $table->string('token')->length(32);
            $table->tinyInteger('currency_type')->length(4)->default(0);
            $table->tinyInteger('merchant_type')->length(4)->default(0);
            $table->decimal('balance', 15, 3)->default(0);
            $table->text('rate_data');
            $table->tinyInteger('status')->length(4)->default(1);
            $table->dateTime('create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('last_update')->default(DB::raw('CURRENT_TIMESTAMP'));
        });

        $data = [
            ['agent_id' => '1','account' => 'test01','token' => '827ccb0eea8a706c4c34a16891f84e7b','currency_type' => '1','merchant_type' => '0','balance' => '99999'],
            ['agent_id' => '1','account' => 'test02','token' => '827ccb0eea8a706c4c34a16891f84e7b','currency_type' => '1','merchant_type' => '0','balance' => '10000'],
        ];
        DB::table('player')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player');
    }
}
