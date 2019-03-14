<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailDisplayShare extends Model
{
    protected $table = 'detail_display_shares';

    protected $fillable = [
        'id_display_share', 'id_category', 'id_brand', 'tier', 'depth'
    ];
    
    public function diplay_share(){
    	return $this->belongsTo(DisplayShare::class);
    }
    public function category(){
    	return $this->belongsTo(Category::class, 'id_category');
    }
    public function brand(){
    	return $this->belongsTo(Brand::class, 'id_brand');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['category_name'] = $this->category->name??'-';
        $array['brand_name']    = $this->brand->name??'-';
        return $array;
    }
}
