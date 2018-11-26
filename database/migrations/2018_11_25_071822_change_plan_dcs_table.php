<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePlanDcsTable extends Migration
{
    public function up()
    {
        Schema::table('plan_dcs', function (Blueprint $table) {
             $table->dropColumn('lokasi');
             $table->string('plan')->after('date');
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
            if (Schema::hasColumn('plan_dcs', 'plan')) {
                $table->dropColumn('plan');
            }
        });
    }
}
