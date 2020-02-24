<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteMeasurementOnProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function($table) {
            $table->dropColumn(['carton']);
            $table->dropColumn(['pack']);
            $table->dropColumn(['pcs']);
            $table->dropColumn(['deskripsi']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('deskripsi')->after('code')->nullable();
            $table->string('pcs')->after('code')->nullable();
            $table->string('pack')->after('code')->nullable();
            $table->string('carton')->after('code')->nullable();
        });
    }
}
