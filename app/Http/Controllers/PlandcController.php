<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\PlanDc;

class PlandcController extends Controller
{
    public function read()
    {
        return view('plandc.plandc');
    }

    public function data()
    {
        $plan = PlanDc::with('planEmployee')
        ->select('plan_dcs.*');
        return Datatables::of($plan)
        ->addColumn('action', function ($plan) {
            return "<a href=".route('ubah.plan', $plan->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
            <button data-url=".route('plan.delete', $plan->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }
}
