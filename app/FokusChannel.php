<?php

namespace App;

use App\Components\traits\DropDownHelper;
use Illuminate\Database\Eloquent\Model;

class FokusChannel extends Model
{
    protected $fillable = [
        'id_channel', 'id_pf'
    ];

    public function channel()
    {
    	return $this->belongsTo('App\Channel', 'id_channel');
    }

    public function pf()
    {
    	return $this->belongsTo('App\ProductFokus', 'id_productfokus');
    }

}
