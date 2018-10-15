<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeSpv extends Model
{
    protected $fillable = [
       'id_employee', 'id_user'
    ];
}
