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

            //table's default charset & collation
            $table->charset = 'utf8mb4';    //Specify a default character set for the table (MySQL).
            $table->collation = 'utf8mb4_unicode_ci';   //Specify a default collation for the table (MySQL).

            //create columns
            $table->increments('id')->primary();  //id
            $table->string('menu_name',32)->nullable(false);    //menu_name
            $table->string('menu_class',32)->nullable(false);    //menu_class
            $table->string('menu_value',32)->nullable(true)->default(null);    //menu_value
            $table->smallInteger('status')->nullable(false)->default(1);    //status

            //foreign key constraints

        });

        //insert data
        $data = [
            ['menu_name' => '首頁','menu_classs' => 'index','menu_value' => 'index'],
            ['menu_name' => '後台管理','menu_class' => 'admin','menu_value' => ''],
            ['menu_name' => '後台帳號','menu_class' => 'admin','menu_value' => 'admin_user'],
            ['menu_name' => '後台權限','menu_class' => 'admin','menu_value' => 'admin_permission'],
            ['menu_name' => '後台操作紀錄','menu_class' => 'admin','menu_value' => 'operation_logs']
        ];
        DB::table('admin_menu')->insert($data);

        //insert data
		// $data = [
		// 	'col' => 'val', ...
		// ];
        // DB::table('admin')->insert($data);

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
        Schema::dropIfExists('admin_menu');
    }
}
