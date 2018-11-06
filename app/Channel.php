<?php

namespace App;

use App\Components\traits\DropDownHelper;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use DropDownHelper;
    protected $fillable = [
        'name'
    ];

    public function accounts()
    {
    	return $this->hasMany('App\Account', 'id_channel');
    }

    public function Fokus()
    {
    	return $this->hasMany('App\FokusChannel', 'id_channel');
    }
}
