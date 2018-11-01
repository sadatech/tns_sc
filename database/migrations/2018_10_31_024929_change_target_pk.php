<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTargetPk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::table('product_knowledges', function (Blueprint $table) {
             $table->dropForeign(['target']);
             $table->dropColumn('target');
        });
        Schema::table('product_knowledges', function (Blueprint $table) {
              $table->unsignedInteger('target')->after('filePDF')->nullable();
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
        Schema::table('product_knowledges', function (Blueprint $table) {
            $table->dropForeign(['target']);
             $table->dropColumn('target');
           
        });   
        Schema::table('product_knowledges', function (Blueprint $table) {
             $table->unsignedInteger('target')->before('filePDF');
             $table->foreign('target')->references('id')->on('positions');
        });
    }
}
