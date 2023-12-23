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
            $table->integer('id')->length(11)->autoIncrement();
            $table->string('title')->length(32);
            $table->string('name')->length(32);
            $table->string('value')->length(256);
            $table->tinyInteger('status')->length(4)->default(1);
        });

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
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_config');
    }
}
