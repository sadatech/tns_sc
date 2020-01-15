<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeAndModelToJobTracesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_traces', function (Blueprint $table) {
            $table->string('file_name')->after('status')->nullable();
            $table->text('file_path')->after('status')->nullable();
            $table->string('directory')->after('status')->nullable();
            $table->string('model')->after('status')->nullable();
            $table->string('type',1)->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_traces', function($table) {
            $table->dropColumn(['type','model','directory','file_path','file_name']);
        });
    }
}
