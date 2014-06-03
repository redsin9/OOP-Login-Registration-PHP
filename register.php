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
				'required' => true,
				'min' => 2,
				'max' => 20,
				'unique' => 'users'
			),

			'password' => array 
			(
				'required' => true,
				'min' => 6
			)
		)
	);

	if ($validation->Passed() == true)
	{
        $user = new User();
        $salt = Hash::Salt(32);


        try
        {
            $user->Create
            (
                array
                (
                    'username' => Input::Get('username'),
                    'password' => Hash::Make(Input::Get('password'), $salt),
                    'salt' => $salt,
                    'name' => Input::Get('name'),
                    'joined' => date('Y-m-d H:i:s'),
                    'group' => 1
                )
            );

            // log this user in
            $user->Login(Input::Get('username'), Input::Get('password'));

            // redirect to homepage
            Session::Put('home', 'User ' . Input::Get('username') . ' has been created successfully.');
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
		        <label for="username">Username: </label>
		        <input type="text" name="username" id="username" value="<?php echo escape(Input::Get('username')); ?>" autocomplete="off" />

	        </div>

	        <div class="field">
		        <label for="password">Password: </label>
		        <input type="password" name="password" id="password" value="" autocomplete="off" />
	        
	        </div>

	        <div class="field">
		        <label for="password_again">Confirm Password: </label>
		        <input type="password" name="password_again" id="password_again" value="" autocomplete="off" />
	        
	        </div>

	        <div class="field">
		        <label for="name">Name: </label>
		        <input type="text" name="name" id="name" value="<?php echo escape(Input::Get('name')); ?>" autocomplete="off" />

	        </div>

            <input type="hidden" name="token" value="<?php echo Token::Generate(); ?>" />
	        <input type="submit" value="Register" />
        </form>
    </body>
</html>
