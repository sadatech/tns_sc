<?php
namespace App\Traits;

trait StringTrait
{

    public function removeFirstQuotes($string)
    {
    	return str_replace("'", "", $string);
    }

}