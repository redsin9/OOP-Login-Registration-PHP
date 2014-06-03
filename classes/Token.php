<?php

class Token
{
    public static function Generate()
    {
        return Session::Put(Config::Get('session/token_name'), md5(uniqid()));
    }



    public static function Check($token)
    {
        $result = true;
        $token_name = Config::Get('session/token_name');


        if(Session::Exists($token_name) && $token === Session::Get($token_name))
        {
            Session::Delete($token_name);
        }
        else
        {
            $result = false;
        }

        return $result;
    }
}


?>