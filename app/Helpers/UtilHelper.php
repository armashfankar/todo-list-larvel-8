<?php

namespace App\Helpers;

class UtilHelper
{
    /**
     * @param bool $onlyNumber
     * @return string
     */
    public static function generateString($onlyNumber = false)
    {
        $timestamp = (microtime(true) * 10000);
        $timestamp = str_replace("0", "8", $timestamp);
        $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
        $number = "123456789";

        $random_string = ($onlyNumber == true) ? str_shuffle($number) : str_shuffle($string);
        $unique_code = str_shuffle(substr($timestamp, 0, 4)) . substr($timestamp, 5, 8) . substr($random_string, 0, 4);

        return $unique_code;
    }

    /**
     * @param $url
     * @return bool|string
     */
    public static function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($ch);
        curl_close ($ch);
        return $return;
    }

}
