<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email',100)->unique();
            $table->string('password');
            $table->enum('level', ['administrator', 'user']);
            $table->enum('email_status', ['verified', 'unverified']);
            $table->rememberToken();
            $table->timestamps();

        });
        DB::table('users')->insert([
            ['name' => 'Nothy', 'email' => 'excelokta@gmail.com', 'password' => bcrypt('dewa123'), 'level' => 'administrator', 'email_status' => 'verified'],
            ['name' => 'Mank Junet', 'email' => 'junet@gmail.com', 'password' => bcrypt('dewa123'), 'level' => 'user', 'email_status' => 'verified'],
            ['name' => 'Benny Wijaya', 'email' => 'bennytirtayasa@gmail.com', 'password' => bcrypt('aquacross50'), 'level' => 'user', 'email_status' => 'unverified'],
        ]);
    }
    
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
