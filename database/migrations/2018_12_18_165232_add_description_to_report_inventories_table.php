<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionToReportInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_inventories', function (Blueprint $table) {
             $table->string('description')->after('photo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_inventories', function($table) {
            if (Schema::hasColumn('report_inventories', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
}
