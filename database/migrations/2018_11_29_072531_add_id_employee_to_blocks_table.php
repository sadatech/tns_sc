<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdEmployeeToBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blocks', function (Blueprint $table) {
             $table->integer('id_employee')->after('id_subarea')->unsigned()->nullable();
             $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blocks', function($table) {
            if (Schema::hasColumn('blocks', 'id_employee')) {
                $table->dropForeign(['id_employee']);
                $table->dropColumn('id_employee');
            }
        });
    }
}
