<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCompetitorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_competitors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_subcategory')->unsigned();
            $table->integer('id_brand')->unsigned();
            $table->unsignedInteger('id_product')->nullable();
            $table->string('deskripsi')->nullable();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_subcategory')->references('id')->on('sub_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_brand')->references('id')->on('brands')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('products', function($table) {
            if (Schema::hasColumn('products', 'id_product')) {
                $table->dropForeign(['id_product']);
                $table->dropColumn('id_product');
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_competitors');

        Schema::table('products', function($table) {
            $table->unsignedInteger('id_product')->after('id_brand')->nullable();

            $table->foreign('id_product')->references('id')->on('products');
        });
    }
}
