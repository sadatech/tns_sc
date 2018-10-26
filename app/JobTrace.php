<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobTrace extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id_user', 'date', 'title', 'results', 'status', 'explanation', 'log'
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
        return $this->hasMany('App\User', 'id_user');
    }
}
