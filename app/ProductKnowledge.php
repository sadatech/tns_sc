<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductKnowledge extends Model
{
    protected $table = 'product_knowledges';
    protected $fillable = ['admin','sender','subject','type','filePDF','target'];
}
