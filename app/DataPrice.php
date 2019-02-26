<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataPrice extends Model
{
    protected $table = 'data_price';

    protected $fillable = [
        'id_store', 'id_employee', 'date', 'week'
    ];

    public function detail_data_price(){
    	return $this->hasMany(DetailDataPrice::class,'id_data_price');
    }

    public function employee(){
    	return $this->belongsTo(Employee::class, 'id_employee');
    }

    public function store(){
    	return $this->belongsTo(Store::class, 'id_store');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['store_name'] = $this->store->name1??'-';
        return $array;
    }
}
