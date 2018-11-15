<?php

namespace App;
use App\Components\traits\DropDownHelper;
use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class Area extends Model
{
    use DropDownHelper;
    protected $fillable = [
        'name', 'id_region'
    ];

    public function region()
    {
        return $this->belongsTo('App\Region', 'id_region');
    }

    public function Fokus()
    {
    	return $this->hasMany('App\FokusArea', 'id_area');
    }
    
    public function subareas()
    {
    	return $this->hasMany('App\SubArea', 'id_area');
    }  

    public function productfocus()
    {
        return $this->hasMany('App\ProductFokus', 'id_area');
    }

    public function productpromo()
    {
        return $this->hasMany('App\ProductPromo', 'id_area');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
