<?php
    
class Config
{
    public static function Get ($path = NULL)
    {
        // get the global configurations
        $config = NULL;

        if ($path)
        {
            $valid = TRUE;      // check if the path is valid
            $config = $GLOBALS['config'];

            // split the $path using '/' delimiter
            $path = explode('/', $path);

            foreach ($path as $bit)
            {
                // check if element $bit exists within the configuration
                if (isset($config[$bit]))
                {
                    $config = $config[$bit];
                }
                else
                {
                    $valid = FALSE;
                    break;
                }
            }

            if ($valid == FALSE)
            {
                $config = NULL;
            }
        }

        return $config;
    }
}


?>