<?php
/**
 * Created by PhpStorm.
 * User: Justin
 * Date: 12/19/2019
 * Time: 3:26 PM
 */

namespace CovidTrack\Domain\Helpers;

class ArrayHelpers
{
    public static function replaceNullValueWithEmptyString(&$value) {
        $value = $value === null ? "" : $value;
    }

    public static function replaceNullValuesWIthEmptyString(array &$array)
    {
        array_walk_recursive($array, [ArrayHelpers::class, "replaceNullValueWithEmptyString"]);
    }

    public static function replaceIntMinWithEmptyString(array &$array)
    {
        array_walk_recursive($array, [StringHelpers::class, "replaceIntMinWithEmptyString"]);
    }

    public static function replaceStringValuesWithInts(array &$array)
    {
        array_walk_recursive($array, [ArrayHelpers::class, "replaceStringValueWithInt"]);
    }

    public static function replaceStringValueWithInt(&$value) {
        if (is_numeric($value))
            $value = intval($value);
    }

    public static function arrayMergePreserveNulls(array $a, array $b) : array
    {
        $c = $b;
        foreach($a as $key => $val)
        {
            if (!array_key_exists($key, $b))
                $c[$key] = $val;
            else if($key == NULL && $b[$key] == NULL)
            {
                $c[$key] = $val;
            } else if($key != NULL && $b[$key] == NULL) {
                $c[$key]= $val;
            } else if($key != NULL && $b[$key] != NULL) {
                $c[$key]= $b[$key];
            } else {
                $c[$key]= $b[$key];
            }
        }
        return $c;
    }

}