<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdditionalDisplay extends Model
{
    protected $table = 'additional_displays';

    protected $fillable = [
        'id_store', 'id_employee', 'date', 'week'
    ];
    
    public function detail_additional_display(){
    	return $this->hasMany(DetailAdditionalDisplay::class,'id_additional_display');
    }

    public function employee(){
    	return $this->belongsTo(Employee::class);
    }

    public function store(){
    	return $this->belongsTo(Store::class);
    }
}
