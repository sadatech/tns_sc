<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAlamatToPlanDcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_dcs', function($table) {
            $table->text('alamat')->after('channel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_dcs', function($table) {
            if (Schema::hasColumn('plan_dcs','alamat')) {
                $table->dropColumn('alamat');
            }
        });
    }
}
