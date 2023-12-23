<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAdminMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_menu', function (Blueprint $table) {
            $table->integer('id')->length(11)->autoIncrement();
            $table->string('menu_name')->length(32);
            $table->string('menu_class')->length(32);
            $table->string('menu_value')->nullable()->length(32);
            $table->tinyInteger('status')->length(4)->default(1);
        });
        
        $data = [
            ['menu_name' => '首頁','menu_classs' => 'index','menu_value' => 'index'],
            ['menu_name' => '後台管理','menu_class' => 'admin','menu_value' => ''],
            ['menu_name' => '後台帳號','menu_class' => 'admin','menu_value' => 'admin_user'],
            ['menu_name' => '後台權限','menu_class' => 'admin','menu_value' => 'admin_permission'],
            ['menu_name' => '後台操作紀錄','menu_class' => 'admin','menu_value' => 'operation_logs']
        ];
        DB::table('admin_menu')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_menu');
    }
}
