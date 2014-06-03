<?php

require_once 'core/init.php';


$user = new User();

// redirect to home page if not logged in
if (!$user->IsLoggedIn())
{
    Redirect::To('index.php');
}

if (Input::Exists() && Token::Check(Input::Get('token')) == true)
{
    $validation = new Validation();

    $validation->Check
        (
            $_POST,
            array
            (
                'name' => array
                (
                    'required' => true,
                    'min' => 2,
                    'max' => 50
                )
            )
        );

    if ($validation->Passed() == true)
    {
        try
        {
            $user->Update
            (
                array
                (
                    'name' => Input::Get('name')
                )
            );

            Session::Put('home', 'Your details has been updated.');
            Redirect::To('index.php');
        }
        catch (Exception $e)
        {
            die ($e->getMessage());
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
                <label for="name">Name: </label>
                <input type="text" name="name" id="name" value="<?php echo escape($user->Data()->name); ?>" />
            </div>

            <input type="hidden" name="token" value="<?php echo Token::Generate(); ?>" />
            <input type="submit" value="Update" />
        </form>
    </body>
</html>
