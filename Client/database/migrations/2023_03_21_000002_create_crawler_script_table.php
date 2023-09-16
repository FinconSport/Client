<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCrawlerScriptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crawler_script', function (Blueprint $table) {
            $table->integer('id')->length(11)->autoIncrement();
            $table->string('crawler_name')->length(32);
            $table->string('crawler_url')->length(32);
            $table->dateTime('begin_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('last_update')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->tinyInteger('status')->length(4)->default(1);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crawler_script');
    }
}
