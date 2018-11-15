<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIstlToEmployeeSubareas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_sub_areas', function($table) {
            $table->boolean('isTl')->default('0')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_sub_areas', function($table) {
            if (Schema::hasColumn('employee_sub_areas','isTl')) {
                $table->dropColumn('isTl');
            }
        });
    }
}
