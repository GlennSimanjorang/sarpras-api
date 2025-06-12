<?php

namespace App\Utility;

class Formatter
{
    public static function removeVowels(string $string)
    {
        $vowels = ["a","i","u","e","o","A","I","U","E","O"];
        return str_replace($vowels, '', $string);
    }
}
