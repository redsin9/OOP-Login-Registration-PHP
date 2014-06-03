<?php

require_once 'core/init.php';

$user = new User();
$user->Logout();

Redirect::To('index.php');
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
