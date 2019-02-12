<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanDc extends Model
{
    protected $fillable = [
        'date', 'stocklist', 'channel', 'plan', 'actual', 'photo'
    ];

    public function planEmployee()
    {
        return $this->hasMany('App\PlanEmployee', 'id_plandc');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['photo_url'] = isset($this->photo) ? str_replace('https:', 'http:', asset('uploads/plan/'.$this->photo)) : null;
        return $array;
    }
}