<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DistributionMotoric extends Model
{
    protected $fillable = [
        'id_employee','id_block','date'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function block()
    {
        return $this->belongsTo('App\Block', 'id_block');
    }

    public function distributionDetail()
    {
        return $this->hasMany('App\DistributionMotoricDetail', 'id_distribution');
    }
}
