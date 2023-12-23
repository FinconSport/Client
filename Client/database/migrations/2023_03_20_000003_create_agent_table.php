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
            $table->integer('id')->length(11)->autoIncrement();
            $table->string('account')->length(32);
            $table->string('email')->length(64);
            $table->string('password')->length(32);
            $table->string('prefix')->length(32);
            $table->tinyInteger('currency_type')->length(4)->default(1);
            $table->tinyInteger('merchant_type')->length(4)->default(1);
            $table->string('token')->length(32);
            $table->tinyInteger('is_beta')->length(1)->default(1);
            $table->text('white_list');
            $table->decimal('balance', 15, 3)->default(0);
            $table->dateTime('create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('last_login')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->tinyInteger('status')->length(4)->default(1);
        });

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
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent');
    }
}
