<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class Store extends Model
{
    
    protected $fillable = [
            'photo', 'name1', 'name2', 'address', 'latitude', 'longitude',
        'id_account', 'id_subarea','id_timezone', 'id_salestier', 'is_vito' ,'is_jawa' ,'store_panel', 'coverage', 'delivery'
    ];

    public function sales()
    {
        return $this->hasOne('App\SalesTiers', 'id', 'id_salestier');
    }
    public function timezone()
    {
        return $this->hasOne('App\Timezone', 'id', 'id_timezone');
    }
    public function distributor()
    {
        return $this->hasMany('App\StoreDistributor', 'id_store');
    }
    public function rejoin()
    {
        return $this->hasMany('App\Rejoin', 'id_store');
    }
    public function employeeStore()
    {
        return $this->hasMany('App\EmployeeStore', 'id_store');
    }
    public function sellin()
    {
        return $this->hasMany('App\SellIn', 'id_store');
    }
    public function headerin()
    {
        return $this->hasMany('App\HeaderIn', 'id_store');
    }
    public function resignStore()
    {
        return $this->hasMany('App\ResignStore', 'id_store');
    }
    public function attendanceDetail()
    {
        return $this->hasMany('App\AttendanceDetail', 'id_store');
    }
    public function account()
    {
        return $this->belongsTo('App\Account', 'id_account');
    }
    public function subarea()
    {
        return $this->belongsTo('App\SubArea', 'id_subarea');
    }
    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
