<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIdAgencyToNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['id_agency']);
            $table->dropColumn(['id_agency']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->integer('id_agency')->after('id_position')->unsigned()->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['id_agency']);
            $table->dropColumn(['id_agency']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->integer('id_agency')->after('id_position')->unsigned();
        });
    }
}
