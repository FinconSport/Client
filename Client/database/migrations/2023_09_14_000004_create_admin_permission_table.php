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

            //table's default charset & collation
            $table->charset = 'utf8mb4';    //Specify a default character set for the table (MySQL).
            $table->collation = 'utf8mb4_unicode_ci';   //Specify a default collation for the table (MySQL).

            //creating columns
            $table->increments('id')->primary();  //id
            $table->string('permission_name',32)->nullable(false)->unique();    //permission_name
            $table->text('permission_data')->nullable(false)->default('[]');    //permission_data
            $table->unsignedSmallInteger('status')->nullable(false)->default(1);    //status

            //foreign key constraints

        });

        //insert data
        $data = [
            'permission_name' => '系統管理者',
            'permission_data' => '["index","admin_user","admin_operation","admin_permission"]'
        ];
        DB::table('admin_permission')->insert($data);
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
        Schema::dropIfExists('admin_permission');
    }
}
