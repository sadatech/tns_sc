<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DisplayShare extends Model
{
    protected $table = 'display_shares';

    protected $fillable = [
        'id_store', 'id_employee', 'date', 'week'
    ];
    
    public function detail_display_share(){
    	return $this->hasMany(DetailDisplayShare::class,'id_display_share');
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
