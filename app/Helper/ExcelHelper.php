<?php

namespace App\Helper;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ExcelHelper
{

    public function mapForProductFocus(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'PRODUCT'       => @$item['product'],
                'AREA'          => @$item['area'],
                'START MONTH'   => @$item['start_month'],
                'END MONTH'     => @$item['end_month'],
            ];
        });
    }

    public function mapForProductPrice(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'PRODUCT'       => @$item['product'],
                'AREA'          => @$item['area'],
                'START MONTH'   => @$item['start_month'],
                'END MONTH'     => @$item['end_month'],
            ];
        });
    }

}