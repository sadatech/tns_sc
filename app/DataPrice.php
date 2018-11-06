<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataPrice extends Model
{
    protected $table = 'data_price';

    protected $fillable = [
        'id_store', 'id_employee', 'date'
    ];

    public function detail_data_price(){
    	return $this->hasMany(DetailDataPrice::class,'id_data_price');
    }

    public function employee(){
    	return $this->belongsTo(Employee::class);
    }

    public function store(){
    	return $this->belongsTo(Store::class);
    }
}
