<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailAdditionalDisplay extends Model
{
    protected $table = 'detail_additional_displays';

    protected $fillable = [
        'id_additional_display', 'id_jenis_display', 'jumlah', 'foto_additional'
    ];
    
    public function additional_display(){
    	return $this->belongsTo(AdditionalDisplay::class);
    }
    public function jenis_display(){
    	return $this->belongsTo(JenisDisplay::class);
    }
}
