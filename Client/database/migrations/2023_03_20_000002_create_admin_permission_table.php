<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAdminPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_permission', function (Blueprint $table) {
            $table->integer('id')->length(11)->autoIncrement();
            $table->string('permission_name')->length(32);
            $table->text('permission_data');
            $table->tinyInteger('status')->length(4)->default(1);
        });

        $data = [
            'permission_name' => '系統管理者',
            'permission_data' => '["index","admin_user","admin_operation","admin_permission"]'
        ];
        DB::table('admin_permission')->insert($data);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_permission');
    }
}
