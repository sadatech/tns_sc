<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportInventori extends Model
{
    protected $table = "report_inventories";

    protected $fillable = ["no_polisi", "id_employee", "id_properti_dc", "quantity", "actual", "status", "photo", "description"];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function properti()
    {
        return $this->belongsTo('App\PropertiDc', 'id_properti_dc');
    }

}
