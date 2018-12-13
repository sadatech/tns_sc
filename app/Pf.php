<?php

namespace App;

use App\Components\traits\DropDownHelper;
use App\Components\traits\ValidationHelper;
use Illuminate\Database\Eloquent\Model;

class Pf extends Model
{
    use DropDownHelper;
    use ValidationHelper;

    protected $fillable = [
        'from', 'to', 'id_category1', 'id_category2'
    ];

    public static function rule()
    {
        return [
            'id_category1'      => 'required|integer',
            'id_category2'      => 'required|integer',
            'from'              => 'required',
            'to'                => 'required'
        ];
    }


    public function category1()
    {
        return $this->belongsTo('App\SubCategory', 'id_category1');
    }

    public function category2()
    {
        return $this->belongsTo('App\SubCategory', 'id_category2');
    }

    public static function hasActivePF($data, $self_id = null)
    {
        $products = Pf::where('id_category1', $data['id_category1'])
            ->where('id_category2', $data['id_category2'])
            ->where('id', '!=', $self_id)
            ->where(function($query) use ($data){
                $query->whereBetween('from', [$data['from'], $data['to']]);
                $query->orWhereBetween('to', [$data['from'], $data['to']]);
            })->count();
        return $products > 0;
    }
}
