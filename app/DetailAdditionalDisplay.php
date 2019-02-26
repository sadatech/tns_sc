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
    	return $this->belongsTo(AdditionalDisplay::class, 'id_additional_display');
    }
    public function jenis_display(){
    	return $this->belongsTo(JenisDisplay::class, 'id_jenis_display');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['jenis_display'] = $this->jenis_display->name??'-';
        return $array;
    }
}
