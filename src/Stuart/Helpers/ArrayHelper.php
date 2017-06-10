<?php

namespace Stuart\Helpers;

class ArrayHelper
{

    /**
     * @param $array
     * @param $key
     * @return value referenced by $key or null of key does not exist.
     */
    public static function getSafe($array, $key)
    {
        return isset($array[$key]) ? $array[$key] : null;
    }
}
