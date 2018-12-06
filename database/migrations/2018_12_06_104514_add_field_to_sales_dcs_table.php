<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToSalesDcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_dcs', function($table) {
            $table->string('icip_icip')->after('place')->nullable();
            $table->string('effective_contact')->after('icip_icip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_dcs', function($table) {
            if (Schema::hasColumn('sales_dcs','icip_icip')) {
                $table->dropColumn('icip_icip');
            }
            if (Schema::hasColumn('sales_dcs', 'effective_contact')) {
                $table->dropColumn('effective_contact');
            }
        });
    }
}
