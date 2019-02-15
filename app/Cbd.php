<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cbd extends Model
{
    protected $fillable = [
        'id_employee', 'id_outlet', 'date', 'photo'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Outlet', 'id_outlet');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['outlet_name'] = $this->outlet->name;
        $array['photo1_url'] = !empty($this->photo) ? str_replace('https:', 'http:', asset('uploads/cbd/'.$this->photo)) : '';
        return $array;
    }
}
