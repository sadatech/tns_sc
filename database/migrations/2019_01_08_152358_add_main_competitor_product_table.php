<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMainCompetitorProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('id_main_competitor')->after('panel')->unsigned()->nullable();

            $table->foreign('id_main_competitor')->references('id')->on('product_competitors')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function($table) {
            if (Schema::hasColumn('id_main_competitor')) {
                $table->dropForeign(['id_main_competitor']);
                $table->dropColumn('id_main_competitor');
            }
        });
    }
}
