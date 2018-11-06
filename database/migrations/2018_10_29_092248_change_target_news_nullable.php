<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTargetNewsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('news', function (Blueprint $table) {
             $table->dropForeign(['target']);
             $table->dropColumn('target');
        });

        Schema::table('news', function (Blueprint $table) {
             $table->unsignedInteger('target')->after('content')->nullable();
             $table->foreign('target')->references('id')->on('positions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['target']);
             $table->dropColumn('target');
           
        });   
        Schema::table('news', function (Blueprint $table) {
             $table->unsignedInteger('target')->before('filePDF');
             $table->foreign('target')->references('id')->on('positions');
        });
    }
}
