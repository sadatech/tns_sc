<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBlocksRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blocks', function($table) {
            if (Schema::hasColumn('blocks', 'id_subarea')) {
                $table->dropForeign(['id_subarea']);
                $table->dropColumn('id_subarea');
            }
        });
        Schema::table('blocks', function (Blueprint $table) {
             $table->integer('id_route')->after('id')->unsigned()->nullable();
             $table->foreign('id_route')->references('id')->on('routes')->onUpdate('cascade')->onDelete('cascade');
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
            if (Schema::hasColumn('blocks', 'id_route')) {
                $table->dropForeign(['id_route']);
                $table->dropColumn('id_route');
            }
        });
        Schema::table('blocks', function (Blueprint $table) {
             $table->integer('id_subarea')->after('id')->unsigned()->nullable();
             $table->foreign('id_subarea')->references('id')->on('sub_areas')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
