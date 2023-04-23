<?php

namespace Biin2013\Tiger\Support;

use Illuminate\Support\Str;

class Helper
{
    /**
     * @param array $data
     * @param int $currentDeep
     * @param int $deep
     * @return array
     */
    public static function arrayKeyToCamel(array $data, int $currentDeep = 1, int $deep = PHP_INT_MAX): array
    {
        $arr = [];
        foreach ($data as $k => $v) {
            $key = is_numeric($k) ? $k : Str::camel($k);
            if ($deep > $currentDeep && is_array($v)) {
                $arr[$key] = self::arrayKeyToCamel($v, ++$currentDeep, $deep);
            } else {
                $arr[$key] = $v;
            }
        }

        return $arr;
    }
}