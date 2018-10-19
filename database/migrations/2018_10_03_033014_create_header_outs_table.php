<?php	
use Illuminate\Support\Facades\Schema;	
use Illuminate\Database\Schema\Blueprint;	
use Illuminate\Database\Migrations\Migration;	
 class CreateHeaderOutsTable extends Migration	
{	
    /**	
     * Run the migrations.	
     *	
     * @return void	
     */	
    public function up()	
    {	
        Schema::create('header_outs', function (Blueprint $table) {	
            $table->increments('id');	
            $table->integer('id_employee')->unsigned();	
            $table->integer('id_store')->unsigned();	
            $table->date('date');	
            $table->integer('week');	
            $table->timestamps();	
             $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');	
            $table->foreign('id_store')->references('id')->on('stores')->onUpdate('cascade')->onDelete('cascade');	
        });	
    }	
     /**	
     * Reverse the migrations.	
     *	
     * @return void	
     */	
    public function down()	
    {	
        Schema::dropIfExists('header_outs');	
    }	
}