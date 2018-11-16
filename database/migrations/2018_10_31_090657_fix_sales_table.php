<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('detail_ins')) {
            Schema::table('detail_ins', function($table) {
                $table->dropColumn(['price']);
                $table->dropColumn(['is_pf']);
                $table->dropColumn(['is_target']);
            });
        }

        if (Schema::hasTable('detail_outs')) {
            Schema::table('detail_outs', function($table) {
                $table->dropColumn(['price']);
                $table->dropColumn(['is_pf']);
            });
        }

        if (Schema::hasTable('stock_details')) {
            Schema::table('stock_details', function($table) {
                $table->dropColumn(['price']);
                $table->dropColumn(['isPf']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('detail_ins')) {
            Schema::table('detail_ins', function($table) {
                $table->integer('price');
                $table->tinyInteger('is_pf');
                $table->tinyInteger('is_target');
            });
        }

        if (Schema::hasTable('detail_outs')) {
            Schema::table('detail_outs', function($table) {
                $table->integer('price');
                $table->tinyInteger('is_pf');
            });
        }

        if (Schema::hasTable('stock_details')) {
            Schema::table('stock_details', function($table) {
                $table->integer('price');
                $table->tinyInteger('isPf');
            });
        }
    }
}
