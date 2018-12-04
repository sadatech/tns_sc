<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentationDc extends Model
{
    protected $fillable = [
        'id_employee','date','place','type','note','photo1','photo2','photo3'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['photo1_url'] = !empty($this->photo1) ? ('uploads/documentation/'.$this->photo1) : '';
        $array['photo2_url'] = !empty($this->photo2) ? ('uploads/documentation/'.$this->photo2) : '';
        $array['photo3_url'] = !empty($this->photo3) ? ('uploads/documentation/'.$this->photo3) : '';
        return $array;
    }
}
