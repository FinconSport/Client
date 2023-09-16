<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSystemConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_config', function (Blueprint $table) {

            //table's default charset & collation
            $table->charset = 'utf8mb4';    //Specify a default character set for the table (MySQL).
            $table->collation = 'utf8mb4_unicode_ci';   //Specify a default collation for the table (MySQL).

            //creating columns
            $table->increments('id')->primary();  //id
            $table->string('title',32)->nullable(false);    //title
            $table->string('name',32)->nullable(false);    //name
            $table->text('value')->nullable(false);    //value
            $table->unsignedSmallInteger('status')->nullable(false)->default(1);    //status

            //foreign key constraints

        });

        //insert data
        $data = [
            ['title' => '版本號','name' => 'version','value' => '1.2.2'],
            ['title' => '網站標題','name' => 'client_title','value' => 'dddddd'],
            ['title' => 'host','name' => 'url','value' => 'shopping166.net'],
            ['title' => '測試模式','name' => 'test_mode','value' => '1'],
            ['title' => '試用天數','name' => 'trial_days','value' => '14'],
            ['title' => '試用方案','name' => 'trial_mode','value' => 'standard'],
            ['title' => '後台白名單','name' => 'admin_whitelist','value' => '0'],
            ['title' => '強制更新','name' => 'force_update','value' => '1'],
            ['title' => '上傳數量','name' => 'upload_image_count','value' => '3']
        ];
        DB::table('system_config')->insert($data);
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
        Schema::dropIfExists('system_config');
    }
}
