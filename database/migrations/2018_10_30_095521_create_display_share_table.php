<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisplayShareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('display_shares', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_store');
            $table->unsignedInteger('id_employee');
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_store')->references('id')->on('stores');
            $table->foreign('id_employee')->references('id')->on('employees');
        });

        Schema::create('detail_display_shares', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_display_share');
            $table->unsignedInteger('id_category');
            $table->unsignedInteger('id_brand');
            $table->unsignedInteger('tier')->nullable();
            $table->unsignedInteger('depth')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_display_share')->references('id')->on('display_shares');
            $table->foreign('id_brand')->references('id')->on('brands');
            $table->foreign('id_category')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_display_shares', function (Blueprint $table) {
            $table->dropForeign(['id_display_share']);
            $table->dropForeign(['id_brand']);
            $table->dropForeign(['id_category']);
        });
        Schema::dropIfExists('detail_display_shares');

        Schema::table('display_shares', function (Blueprint $table) {
            $table->dropForeign(['id_store']);
            $table->dropForeign(['id_employee']);
        });
        Schema::dropIfExists('display_shares');
    }
}
