<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = [
        'id_employee', 'id_store', 'date', 'image1', 'image2', 'image3'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'id_store');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['store_name'] = $this->store->name1??'-';
        $array['photo1_url'] = !empty($this->image1) ? str_replace('https:', 'http:', asset('uploads/promo/'.$this->image1)) : '';
        $array['photo2_url'] = !empty($this->image2) ? str_replace('https:', 'http:', asset('uploads/promo/'.$this->image2)) : '';
        $array['photo3_url'] = !empty($this->image3) ? str_replace('https:', 'http:', asset('uploads/promo/'.$this->image3)) : '';
        return $array;
    }

}