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
        $array['photo1_url'] = ('uploads/documentation/'.$this->photo1);
        return $array;
    }
}
