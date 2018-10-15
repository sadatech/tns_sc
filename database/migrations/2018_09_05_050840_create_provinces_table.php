<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateProvincesTable extends Migration
{
    public function up()
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        
        $json = file_get_contents(storage_path('province.json'));
        $objs = json_decode($json,true);
        foreach ($objs as $obj)  {
            foreach ($obj as $key => $value) {
                $insertArr[str_slug($key,'_')] = $value;
            }   
            DB::table('provinces')->insert($insertArr);
        }
    }
    
    public function down()
    {
        Schema::dropIfExists('provinces');
    }
}
