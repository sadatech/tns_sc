<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWeekToAdditionalDisplaysTableDisplaySharesTableAvailabilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('availability', function($table) {
            $table->integer('week')->after('date');
        });
        Schema::table('additional_displays', function($table) {
            $table->integer('week')->after('date');
        });
        Schema::table('display_shares', function($table) {
            $table->integer('week')->after('date');
        });
        Schema::table('data_price', function($table) {
            $table->integer('week')->after('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('availability', function($table) {
            if (Schema::hasColumn('availability', 'week')) {
                $table->dropColumn('week');
            }
        });
        Schema::table('additional_displays', function($table) {
            if (Schema::hasColumn('additional_displays', 'week')) {
                $table->dropColumn('week');
            }
        });
        Schema::table('display_shares', function($table) {
            if (Schema::hasColumn('display_shares', 'week')) {
                $table->dropColumn('week');
            }
        });
        Schema::table('data_price', function($table) {
            if (Schema::hasColumn('data_price', 'week')) {
                $table->dropColumn('week');
            }
        });
    }
}
