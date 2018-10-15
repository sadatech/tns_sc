<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCitiesTable extends Migration
{
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_province')->unsigned();
            $table->string('name');
            $table->timestamps();

            $table->foreign('id_province')->references('id')->on('provinces')->onUpdate('cascade')->onDelete('cascade');
        });

        $json = file_get_contents(storage_path('city.json'));
        $objs = json_decode($json,true);
        foreach ($objs as $obj)  {
            foreach ($obj as $key => $value) {
                $insertArr[str_slug($key,'_')] = $value;
            }   
            DB::table('cities')->insert($insertArr);
        }
    }

    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
