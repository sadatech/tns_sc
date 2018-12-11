<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoPolisiToReportInvetoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_inventories', function (Blueprint $table) {
             $table->string('no_polisi')->after('id')->nullable();
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
            if (Schema::hasColumn('report_inventories', 'no_polisi')) {
                $table->dropColumn('no_polisi');
            }
        });
    }
}
