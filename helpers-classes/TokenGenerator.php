<?php

namespace App\Helpers;

class TokenGenerator
{
    public static function generate($length = 32)
    {
        $token = "";
        for ($i = 0; $i < $length; $i++) {
            $token .= rand(0, 9);
        }
        return $token;
    }
}