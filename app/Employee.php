<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use App\Filters\QueryFilters;

class Employee extends Model implements AuthenticatableContract, JWTSubject
{
    use Authenticatable;
    
    protected $fillable = [
        'name', 'nik', 'id_position', 'ktp', 'phone', 'email', 'rekening', 'bank', 'status', 'joinAt', 'id_agency', 'gender', 'education', 'birthdate', 'foto_ktp', 'foto_tabungan', 'isResign', 'password', 'id_timezone'
    ];

    protected $hidden = [
        'password'
    ];
    
    public function resigns()
    {
        return $this->hasMany('App\Resign', 'id_employee');
    }

    public function attendanceDetail()
    {
        return $this->hasMany('App\AttendanceDetail', 'id_employee');
    }

    public function rejoins()
    {
        return $this->hasMany('App\Rejoin', 'id_employee');
    }

    public function sellin()
    {
        return $this->hasMany('App\SellIn', 'id_employee');
    }

    public function headeriin()
    {
        return $this->hasMany('App\HeaderIn', 'id_employee');
    }

    public function employeeStore()
    {
        return $this->hasMany('App\EmployeeStore', 'id_employee');
    }

    public function position()
    {
        return $this->belongsTo('App\Position', 'id_position');
    }

    public function agency()
    {
        return $this->belongsTo('App\Agency', 'id_agency');
    }

    public function timezone()
    {
        return $this->belongsTo('App\Timezone', 'id_timezone');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}

