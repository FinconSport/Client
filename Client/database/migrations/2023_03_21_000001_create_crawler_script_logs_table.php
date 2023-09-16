<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCrawlerScriptLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crawler_script_logs', function (Blueprint $table) {
            $table->integer('id')->length(11)->autoIncrement();
            $table->string('crawler_name')->length(32);
            $table->string('crawler_url')->length(32);
            $table->text('crawler_result');
            $table->tinyInteger('is_error')->length(1)->default(1);
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
        Schema::dropIfExists('crawler_script_logs');
    }
}
