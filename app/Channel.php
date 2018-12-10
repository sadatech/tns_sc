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

    public static function defaultChannel()
    {
    	return self::get()->first();
    }

    public function accounts()
    {
    	return $this->hasMany('App\Account', 'id_channel');
    }

    public function Fokus()
    {
        return $this->hasMany('App\FokusChannel', 'id_channel');
    }

    public function fokusMtc()
    {
        return $this->hasMany('App\ProductFokusMtc', 'id_channel');
    }
}
