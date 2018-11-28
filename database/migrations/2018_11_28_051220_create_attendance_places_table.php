<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendancePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_places', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_attendance')->unsigned();
            $table->integer('id_place')->unsigned()->nullable();
            $table->datetime('checkin');
            $table->datetime('checkout')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_place')->references('id')->on('places')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('attendance_places');
    }
}
