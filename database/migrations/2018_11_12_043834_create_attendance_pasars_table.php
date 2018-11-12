<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendancePasarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_pasars', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_attendance')->unsigned();
            $table->integer('id_pasar')->unsigned()->nullable();
            $table->datetime('checkin');
            $table->datetime('checkout')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_pasar')->references('id')->on('pasars')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_attendance')->references('id')->on('attendances')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_pasars');
    }
}
