<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $table = 'availability';

    protected $fillable = [
        'id_store', 'id_employee', 'date', 'week'
    ];
    
    public function detail_availability(){
    	return $this->hasMany(DetailAvailability::class,'id_availability');
    }

    public function employee(){
    	return $this->belongsTo(Employee::class);
    }

    public function store(){
    	return $this->belongsTo(Store::class, 'id_store');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['store_name'] = $this->store->name1?? '-';
        return $array;
    }
}
