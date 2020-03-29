<?php
/**
 * Created by PhpStorm.
 * User: Justin
 * Date: 3/28/2020
 * Time: 5:13 PM
 */

namespace CovidTrack\Domain\Helpers;

class StringHelpers
{
    public Static function split2($string,$needle,$nth)
    {
        $max = strlen($string);
        $n = 0;
        for($i=0;$i<$max;$i++)
        {
            if($string[$i]==$needle)
            {
                $n++;
                if($n>=$nth)
                {
                    break;
                }
            }
        }
        return substr($string,$i+1,$max);
    }

    public static function replaceIntMinWithEmptyString(&$value) : void
    {
        $value = $value === -268435456 ? '' : $value;
        $value = $value === '-268435456' ? '' : $value;
    }
}