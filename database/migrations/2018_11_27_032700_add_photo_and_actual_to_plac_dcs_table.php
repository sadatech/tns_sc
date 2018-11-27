<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPhotoAndActualToPlacDcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_dcs', function (Blueprint $table) {
             $table->string('actual')->after('channel')->nullable();
             $table->text('photo')->after('actual')->nullable();
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
            if (Schema::hasColumn('plan_dcs', 'actual')) {
                $table->dropColumn('actual');
            }
            if (Schema::hasColumn('plan_dcs', 'photo')) {
                $table->dropColumn('photo');
            }
        });
    }
}
