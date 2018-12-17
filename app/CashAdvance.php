<?php

namespace App;
use App\Components\traits\DropDownHelper;
use App\Filters\QueryFilters;

use Illuminate\Database\Eloquent\Model;

class CashAdvance extends Model
{
    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
    
    protected $fillable = [
		'id_employee', 'id_area', 'date', 'description', 'km_begin', 'km_end', 'km_distance', 'tpd', 'hotel', 'bbm', 'parking_and_toll', 'raw_material', 'property', 'permission', 'bus', 'sipa', 'taxibike', 'rickshaw', 'taxi','other_currency', 'other_description', 'total_cost'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function area()
    {
        return $this->belongsTo('App\Area', 'id_area');
    }
}