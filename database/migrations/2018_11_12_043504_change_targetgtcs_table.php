<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTargetgtcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('target_gtcs', function($table) {
            $table->dropColumn('value');
            $table->dropColumn('value_pf');
            $table->integer('hk')->after('id_employee');
            $table->integer('pf')->after('id_employee');
            $table->integer('value_sales')->after('id_employee');
            $table->integer('ec')->after('id_employee');
            $table->integer('cbd')->after('id_employee');
            $table->dropForeign(['id_pasar']);
            $table->dropColumn(['id_pasar']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('target_gtcs', function($table) {
            $table->integer(['value']);
            $table->integer(['value_pf']);
            $table->integer('id_pasar')->unsigned()->after('id_employee');
            $table->foreign('id_pasar')->references('id')->on('pasars')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
