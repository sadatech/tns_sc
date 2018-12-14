<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashAdvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_advances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_employee')->unsigned();
            $table->integer('id_area')->unsigned();
            $table->date('date');
            $table->text('description')->nullable();
            $table->integer('km_begin')->default(0);
            $table->integer('km_end')->default(0);
            $table->integer('km_distance')->default(0);
            $table->double('tpd')->default(0);
            $table->double('hotel')->default(0);
            $table->double('bbm')->default(0);
            $table->double('parking_and_toll')->default(0);
            $table->double('raw_material')->default(0);
            $table->double('property')->default(0);
            $table->double('permission')->default(0);
            $table->double('bus')->default(0);
            $table->double('sipa')->default(0);
            $table->double('taxibike')->default(0);
            $table->double('rickshaw')->default(0);
            $table->double('taxi')->default(0);
            $table->double('other_cost')->default(0);
            $table->text('other_description')->nullable();
            $table->double('total_cost')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_area')->references('id')->on('areas')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_advances');
    }
}
