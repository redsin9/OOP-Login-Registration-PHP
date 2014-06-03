<?php

class Cookie
{
    public static function Exists($name)
    {
        return isset($_COOKIE[$name]);
    }



    public static function Get($name)
    {
        return $_COOKIE[$name];
    }



    public static function Put ($name, $value, $expiry)
    {
        $result = true;


        if (setcookie($name, $value, time() + $expiry, '/') == false)
        {
            $result = false;
        }

        return $result;
    }



    public static function Delete($name)
    {
        self::Put($name, '', -1);
    }
}


?>

