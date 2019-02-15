<?php
namespace App\Traits;

trait StringTrait
{

    public function removeFirstQuotes($string)
    {
    	return str_replace("'", "", $string);
    }

    public function numberToPrice($currency, $string)
    {
    	return $currency.'. '.number_format($string);
    }

}