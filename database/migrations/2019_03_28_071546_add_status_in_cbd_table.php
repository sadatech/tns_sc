<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusInCbdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_cbds', function (Blueprint $table) {
            $table->tinyInteger('propose')->after('total_hanger')->default(1);
            $table->tinyInteger('approve')->after('total_hanger')->default(0);
            $table->tinyInteger('reject')->after('total_hanger')->default(0);
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
            $table->dropColumn(['reject']);
            $table->dropColumn(['approve']);
            $table->dropColumn(['propose']);
        });
    }
}
