<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_position')->unsigned();
            $table->integer('id_agency')->unsigned();
            $table->integer('id_timezone')->unsigned();
            $table->integer('id_subarea')->unsigned()->nullable();
            $table->string('name');
            $table->string('nik');
            $table->string('ktp')->unique();
            $table->string('phone')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('rekening')->nullable();
            $table->string('bank')->nullable();
            $table->enum('status', ['Stay', 'Mobile'])->nullable();
            $table->date('joinAt');
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->enum('education', ['SD', 'SMP', 'SLTA', 'D1', 'D2', 'D3', 'S1/D4', 'S2']);
            $table->date('birthdate');
            $table->text('foto_ktp')->nullable();
            $table->text('foto_tabungan')->nullable();
            $table->string('password');
            $table->boolean('isResign')->default('0');
            $table->timestamps();

            $table->foreign('id_subarea')->references('id')->on('sub_areas')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_position')->references('id')->on('positions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_agency')->references('id')->on('agencies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_timezone')->references('id')->on('timezones')->onUpdate('cascade')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
