<?php

class Hash
{
    public static function Make($string, $salt = '')
    {
        $result = null;


        $result = hash('sha256', $string . $salt);

        return $result;
    }



    public static function Salt($length)
    {
        $result = null;


        $result = mcrypt_create_iv($length);

        return $result;
    }



    public static function Unique()
    {
        $result = null;


        $result = self::Make(uniqid());

        return $result;
    }
}



?>

