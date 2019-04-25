<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DcSetting extends Model
{
    protected $fillable = [
        'code_channel',
        'target'
    ];

}
