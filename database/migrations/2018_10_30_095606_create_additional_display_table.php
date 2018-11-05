<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdditionalDisplayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_displays', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_store');
            $table->unsignedInteger('id_employee');
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_store')->references('id')->on('stores');
            $table->foreign('id_employee')->references('id')->on('employees');
        });

        Schema::create('detail_additional_displays', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_additional_display');
            $table->unsignedInteger('id_jenis_display');
            $table->string('jumlah');
            $table->string('foto_additional');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_additional_display')->references('id')->on('additional_displays');
            $table->foreign('id_jenis_display')->references('id')->on('jenis_displays');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_additional_displays', function (Blueprint $table) {
            $table->dropForeign(['id_additional_display']);
            $table->dropForeign(['id_jenis_display']);
        });
        Schema::dropIfExists('detail_additional_displays');

        Schema::table('additional_displays', function (Blueprint $table) {
            $table->dropForeign(['id_store']);
            $table->dropForeign(['id_employee']);
        });
        Schema::dropIfExists('additional_displays');
    }
}
