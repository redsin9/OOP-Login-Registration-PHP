<?php

require_once 'core/init.php';


$user = new User();

// redirect to home page if not logged in
if (!$user->IsLoggedIn())
{
    Redirect::To('index.php');
}



if (Input::Exists() && Token::Check(Input::Get('token')))
{
    $validation = new Validation();

    $validation->Check
    (
        $_POST,
        array
        (
            'password_current' => array
            (
                'required' => true,
                'min' => 6
            ),

            'password_new' => array
            (
                'required' => true,
                'min' => 6
            ),

            'password_new_again' => array
            (
                'required' => true,
                'min' => 6,
                'matches' => 'password_new'
            )
        )
    );

    if ($validation->Passed() == true)
    {
        // check if the password is matched
        $hash = Hash::Make(Input::Get('password_current'), $user->Data()->salt);

        if ($hash !== $user->Data()->password)
        {
            // wrong password
            echo 'Your current password is wrong';
        }
        else
        {
            // generate new salt
            $salt = Hash::Salt(32);

            // update new password with salt
            $user->Update
            (
                array
                (
                    'password' => Hash::Make(Input::Get('password_new'), $salt),
                    'salt' => $salt
                )
            );


            Session::Put('home', 'Your password has been changed.');
            Redirect::To('index.php');
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
                <label for="password_current">Old password: </label>
                <input type="password" name="password_current" id="password_current" />

            </div>

            <div class="field">
                <label for="password_new">New password: </label>
                <input type="password" name="password_new" id="password_new" />

            </div>

            <div class="field">
                <label for="password_new_again">Confirm new password: </label>
                <input type="password" name="password_new_again" id="password_new_again" />

            </div>

            <input type="hidden" name="token" value="<?php echo Token::Generate(); ?>" />
            <input type="submit" value="Change" />
        </form>
    </body>
</html>
