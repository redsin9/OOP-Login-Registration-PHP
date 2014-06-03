<?php

require_once 'core/init.php';

$username = Input::Get('username');

// check if username is provided
if ($username)
{
    $user = new User($username);

    // check if this user exists
    if ($user->Exists())
    {
        // check if this user is currently logged in
        if ($user->IsLoggedIn())
        {
            $data = $user->Data();

            echo 'Username: ' . $data->username . '<br/>';
            echo 'Full Name: ' . $data->name . '<br/>';
        }
        else
        {
            Redirect::To('index.php');
        }
    }
    else
    {
        Redirect::To(404);
    }
}
else
{
    Redirect::To('index.php');
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        
    </body>
</html>
