<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNewCbdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_cbds', function($table) {
            $table->dropColumn(['posm_shop_sign','posm_others',
                'posm_hangering_mobile','posm_poster','cbd_competitor_detail',
                'cbd_competitor']);
        });
        Schema::table('new_cbds', function (Blueprint $table) {
            $table->string('cbd_competitor')->after('photo')->nullable();
            $table->string('posm')->after('photo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_cbds', function($table) {
            $table->dropColumn(['posm','cbd_competitor']);
        });
        Schema::table('new_cbds', function (Blueprint $table) {
            $table->tinyInteger('posm_shop_sign')->after('photo')->nullable();
            $table->tinyInteger('posm_others')->after('photo')->nullable();
            $table->tinyInteger('posm_hangering_mobile')->after('photo')->nullable();
            $table->tinyInteger('posm_poster')->after('photo')->nullable();
            $table->string('cbd_competitor_detail')->after('photo')->nullable();
            $table->tinyInteger('cbd_competitor')->after('photo')->nullable();
        });
    }
}
