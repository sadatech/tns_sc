<?php

namespace App;

use App\Components\traits\ValidationHelper;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use ValidationHelper;

    public static function rule()
    {
        return [
            'id_employee' => 'integer|required',
            'rilis' => 'required',
            'file' => 'required'
            // 'id_store' => 'integer|required',
        ];
    }

    protected $fillable = ['id_employee', 'id_store', 'rilis', 'id_product', 'quantity'];

    public function targetDetail()
    {
    	return $this->hasMany('App\TargetDetail', 'id_target');
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'id_store');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
}
