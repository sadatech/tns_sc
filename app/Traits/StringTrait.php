<?php
namespace App\Traits;

trait StringTrait
{

    public function removeFirstQuotes($string)
    {
    	return str_replace("'", "", $string);
    }

    public function numberToPrice($currency = '', $string, $separator = '')
    {
    	$price = ($currency != '' ? $currency.'. ' : '').number_format($string);
        return $separator != '' ? str_replace(',', $separator, $price) : $price;
    }

    public function trimAndUpper($string)
    {
    	return strtoupper(trim($string));
    }

    public function getFirstExplode($string, $splitter)
    {
    	return explode($splitter, $string)[0];
    }

}