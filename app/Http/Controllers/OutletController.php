<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Filters\OutletFilters;
use App\Outlet;
use App\EmployeePasar;

class OutletController extends Controller
{
    public function getDataWithFilters(OutletFilters $filters)
    {
        $data = Outlet::filter($filters)->get();
        return $data;
    }
}
