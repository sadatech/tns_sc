<?php

use App\ProductStockType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductStockTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('quantity');
            $table->timestamps();
        });

        DB::table('product_stock_types')->insert([
            ['name' => 'Slow Moving', 'quantity' => 6, 'created_at' => Carbon\Carbon::now()],
            ['name' => 'Fast Moving', 'quantity' => 12, 'created_at' => Carbon\Carbon::now()]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_stock_types');
    }
}
