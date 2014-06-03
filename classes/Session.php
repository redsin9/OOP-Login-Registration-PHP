<?php

class Session
{
    public static function Put($name, $value)
    {
        return $_SESSION[$name] = $value;
    }



    public static function Get($name)
    {
        return $_SESSION[$name];
    }



    public static function Exists($name)
    {
        return isset($_SESSION[$name]);
    }



    public static function Delete($name)
    {
        if (self::Exists($name))
        {
            unset($_SESSION[$name]);
        }
    }



    // if the item existed, get it value and delete it right after that
    // otherwise, create new item and assign the value for it
    public static function Flash($name, $value = null)
    {
        $result = null;

        // check if the item exists in the session
        if (self::Exists($name))
        {
            // backup session value of the item
            $result = self::Get($name);

            // then delete the item within the session
            self::Delete($name);
        }
        else
        {
            // store the item into session with value
            self::Put($name, $value);
        }

        return $result;
    }
}



?>
