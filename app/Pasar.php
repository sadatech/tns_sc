<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pasar extends Model
{
    protected $fillable = [
        'name', 'phone', 'address', 'latitude', 'longitude', 'id_subarea', 'id_timezone'
    ];

    public function subarea()
    {
        return $this->belongsTo('App\SubArea', 'id_subarea');
    }

    public function timezone()
    {
        return $this->belongsTo('App\Timezone', 'id_timezone');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }

}
