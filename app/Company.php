<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'id_province', 'id_city', 'logo', 'username', 'name', 'email', 'address', 'phone', 'fax', 'postal_code', 'token', 'typePrice', 'typeStock'
    ];
}
