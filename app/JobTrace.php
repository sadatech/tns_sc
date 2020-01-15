<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class JobTrace extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id_user', 'date', 'title', 'model', 'directory', 'type', 'file_path', 'file_name', 'results', 'status', 'explanation', 'log'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

	/**
     * Relation Method(s).
     *
     */

    public function user()
    {
        return $this->belongsTo('App\User', 'id_user');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
