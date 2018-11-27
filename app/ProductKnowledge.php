<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductKnowledge extends Model
{
    protected $table = 'product_knowledges';
    protected $fillable = ['admin','sender','subject','type','filePDF','target'];

    public function toArray(){
        $array = parent::toArray();
        $array['file_pdf_url'] = asset('uploads/PKFilePDF/'.$this->filePDF);
        return $array;
    }
}
