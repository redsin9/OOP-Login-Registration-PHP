<?php

require_once 'core/init.php';

if (Input::Exists() && Token::Check(Input::Get('token')) == true)
{
    $validation = new Validation();

    $validation->Check
    (
        $_POST,
        array
        (
            'username' => array
            (
                'required' => true
            ),

            'password' => array
            (
                'required' => true
            )
        )
    );

    if ($validation->Passed() == true)
    {
        $user = new User();
        $remember = (Input::Get('remember') === 'on') ? true : false;
        $login = $user->Login(Input::Get('username'), Input::Get('password'), $remember);


        if ($login)
        {
            echo 'Success';
            Redirect::To('index.php');
        }
        else
        {
            echo 'Fail';
        }
    }
    else
    {
        // show the error messages
        foreach ($validation->Errors() as $error)
        {
            echo $error . "<br/>";
        }
    }
}



?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        <form action="" method="post">
            <div class="field">
                <label for="username">Username: </label>
                <input type="text" name="username" id="username" autocomplete="off" />
            </div>

            <div class="field">
                <label for="password">Password: </label>
                <input type="password" name="password" id="password" autocomplete="off" />
            </div>

            <div class="field">
                <label for="remember">
                    <input type="checkbox" name="remember" id="remember" />Remember Me
                </label>

            </div>

            <input type="hidden" name="token" value="<?php echo Token::Generate(); ?>" />
            <input type="submit" value="Log in" />
        </form>
    </body>
</html>
