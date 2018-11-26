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
    	return $this->belongsTo(Employee::class);
    }

    public function store(){
    	return $this->belongsTo(Store::class);
    }
}
