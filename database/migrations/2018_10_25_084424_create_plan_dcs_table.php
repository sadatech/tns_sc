<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanDcsTable extends Migration
{
    public function up()
    {
        Schema::create('plan_dcs', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->string('lokasi');
            $table->string('stocklist')->nullable();
            $table->enum('channel', ['MTC', 'GTC', 'ITC'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plan_dcs');
    }
}
