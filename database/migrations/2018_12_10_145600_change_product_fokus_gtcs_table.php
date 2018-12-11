<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeProductFokusGtcsTable extends Migration
{
    public function up()
    {
        Schema::table('product_fokus_gtcs', function (Blueprint $table) {
             $table->dropForeign(['id_area']);
             $table->dropColumn('id_area');
        });

        Schema::table('product_fokus_gtcs', function (Blueprint $table) {
             $table->unsignedInteger('id_area')->after('id-product')->nullable();
             $table->foreign('id_area')->references('id')->on('areas')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
         Schema::table('product_fokus_gtcs', function (Blueprint $table) {
            $table->dropForeign(['id_area']);
             $table->dropColumn('id_area');
           
        });   
        Schema::table('product_fokus_gtcs', function (Blueprint $table) {
             $table->unsignedInteger('id_area')->before('created_at');
             $table->foreign('id_area')->references('id')->on('areas')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
