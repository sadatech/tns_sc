<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewCbd extends Model
{
    protected $fillable = [
        'id_employee', 'id_outlet', 'date', 'photo', 'posm_shop_sign', 'posm_others', 'posm_hangering_mobile', 'posm_poster', 'cbd_competitor_detail', 'cbd_competitor', 'cbd_position', 'outlet_type', 'total_hanger', 
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
        $array['photo1_url'] = !empty($this->photo) ? ('uploads/cbd/'.$this->photo) : '';
        return $array;
    }
}
