<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeBrand extends Model
{
    protected $fillable = [
        'id_employee'
     ];

     public function employee()
     {
         return $this->belongsTo('App\Employee', 'id_employee');
     }

}
