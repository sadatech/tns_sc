<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JenisDisplay extends Model
{
    protected $table = 'jenis_displays';

    protected $fillable = [
        'name', 'description'
    ];
    
    public function detail_additional_display(){
    	return $this->hasMany(DetailAdditionalDisplay::class,'id_additional_display');
    }

}
