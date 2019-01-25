<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Oos extends Model
{
    protected $table = 'oos';

    protected $fillable = [
        'id_store', 'id_employee', 'date', 'week'
    ];
    
    public function detail(){
    	return $this->hasMany(OosDetail::class,'id_oos');
    }

    public function employee(){
    	return $this->belongsTo(Employee::class);
    }

    public function store(){
    	return $this->belongsTo(Store::class);
    }
}
