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

    public function createFileCode()
    {
        return "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
    }

    public function createUniqueCode()
    {
        return "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
    }
    
    public function setFileName($text, $length = 140)
    {
        $stringExplode = explode('[',$text);
        if (count($stringExplode) < 2) {
            return $text;
        }
        $string = $stringExplode[1];

        return $stringExplode[0]. '['. substr($string,0,$length) . (strlen($string)>$length?'..]':']');
    }

}