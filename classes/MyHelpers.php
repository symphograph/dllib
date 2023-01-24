<?php

class MyHelpers
{
    public static function isArrayIntList(array $arr): bool
    {
        return self::isArrayInt($arr) && array_is_list($arr);
    }

    public static function isArrayInt(array $arr): bool
    {
        foreach ($arr as $a){
            if(!is_int($a))
                return false;
        }
        return true;
    }
}