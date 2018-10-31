<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdjustmentTargetMTCTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('targets', function($table) {
            $table->unsignedInteger('quantity')->after('id_store');
            $table->unsignedInteger('id_product')->after('id_store');

            $table->dropColumn(['value', 'value_pf']);

            $table->foreign('id_product')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('targets', function($table) {
            if (Schema::hasColumn('targets', 'id_product')) {
                $table->dropForeign(['id_product']);
                $table->dropColumn('id_product');
            }
            if (Schema::hasColumn('targets', 'quantity')) {
                $table->dropColumn('quantity');
            }
            
            if (!Schema::hasColumn('targets', 'value_pf')) {
                $table->integer('value_pf')->after('rilis');
            }
            if (!Schema::hasColumn('targets', 'value')) {
                $table->integer('value')->after('rilis');
            }
        });
    }
}
