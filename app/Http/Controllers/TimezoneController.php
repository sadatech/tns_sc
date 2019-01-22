<?php

namespace App\Http\Controllers;

use App\Timezone;
use App\Filters\TimezoneFilters;

class TimezoneController extends Controller
{

    public function getDataWithFilters(TimezoneFilters $filters)
    {
        $data = Timezone::filter($filters)->get();
        return $data;
    }
}
