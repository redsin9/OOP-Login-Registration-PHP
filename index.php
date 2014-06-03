<?php
    
require_once 'core/init.php';


echo Session::Flash('home') . '<br/>';

// get current user
$user = new User();


if ($user->IsLoggedIn())
{
?>
    <p>
        Hello
        <a href="profile.php?username=<?php echo escape(($user->Data()->username)); ?>">
            <?php echo escape(($user->Data()->name)); ?>
        </a>
    </p>

    <ul>
        <li><a href="logout.php">Log out</a></li>
        <li><a href="update.php">Update details</a></li>
        <li><a href="changepassword.php">Change password</a></li>
    </ul>

<?php

    if ($user->HasPermission('admin'))
    {
        echo '<p>You are an administrator</p>';
    }
}
else
{
?>
    You need to <a href="login.php">log in</a> or <a href="register.php">register</a>
<?php
}

?>