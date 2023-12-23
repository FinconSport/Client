<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateClientMarqueeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_marquee', function (Blueprint $table) {
            $table->integer('id')->length(11)->autoIncrement();
            $table->string('marquee')->length(32);
            $table->tinyInteger('status')->length(4)->default(1);
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
        Schema::dropIfExists('client_marquee');
    }
}
