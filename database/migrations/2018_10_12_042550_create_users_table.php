<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('user_role', function (Blueprint $table) {
            $table->increments('id');
            $table->string('level');
            $table->timestamps();
        });

        DB::table('user_role')->insert([
            ['level' => 'MasterAdmin'],
            ['level' => 'Administrator'],
            ['level' => 'User'],
        ]);
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email',100)->unique();
            $table->string('password');
            $table->unsignedInteger('role_id');
            $table->enum('email_status', ['verified', 'unverified']);
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('role_id')->references('id')->on('user_role');

        });
        DB::table('users')->insert([
            ['name' => 'Sadatech', 'email' => 'sada@gmail.com', 'password' => bcrypt('admin'), 'role_id' => '1', 'email_status' => 'verified'],
              ['name' => 'Sasa', 'email' => 'sasa@gmail.com', 'password' => bcrypt('admin'), 'role_id' => '2', 'email_status' => 'verified'],
              ['name' => 'SasaUser', 'email' => 'sasauser@gmail.com', 'password' => bcrypt('user123'), 'role_id' => '3', 'email_status' => 'verified'],
        ]);
    }
    
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
