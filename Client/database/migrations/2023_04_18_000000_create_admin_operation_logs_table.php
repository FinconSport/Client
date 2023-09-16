<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAdminOperationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_operation_logs', function (Blueprint $table) {
            $table->integer('id')->length(11)->autoIncrement();
            $table->string('action')->length(32);
            $table->string('account')->length(32);
            $table->text('before_data');
            $table->text('after_data');
            $table->dateTime('create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->tinyInteger('status')->length(4)->default(1);
        });

    }

    /**
     * Reverse the migrations.
     * 
     * 
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_operation_logs');
    }
}
