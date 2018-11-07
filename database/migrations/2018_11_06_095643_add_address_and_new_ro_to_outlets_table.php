<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressAndNewRoToOutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outlets', function($table) {
            $table->text('address')->after('phone')->nullable();
            $table->date('new_ro')->after('address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outlets', function($table) {
            if (Schema::hasColumn('outlets','address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('outlets', 'new_ro')) {
                $table->dropColumn('new_ro');
            }
        });
    }
}
