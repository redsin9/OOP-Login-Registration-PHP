<?php

class Redirect
{
    public static function To($location = null)
    {
        if ($location)
        {
            // check if the location is error code. E.g: 404
            if (is_numeric($location))
            {
                switch ($location)
                {
                    case 404:
                        header('HTTP/1.0 404 Not Found');
                        include 'includes/errors/404.php';
                        break;
                }
            }
            else
            {
                header('Location: ' . $location);
            }
        }

        exit();
    }
}


?>

