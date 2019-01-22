<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteIdBrandInPromosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::table('promos', function($table) {
            $table->dropForeign(['id_brand']);
            $table->dropColumn(['id_brand']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->integer('id_brand')->unsigned()->after('id_employee');
            $table->foreign('id_brand')->references('id')->on('brands')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
