<?php
    
session_start();


// define some configurations
$GLOBALS['config'] = array
(
    'mysql' => array
    (
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'db' => 'login_registration'
    ),

    'remember' => array
    (
        'cookie_name' => 'hash',
        'cookie_expiry' => 604800
    ),

    'session' => array
    (
        'session_name' => 'user',
        'token_name' => 'token'
    ),
);


// make requiring classes easier
spl_autoload_register
(
    function($class)
    {
        require_once 'classes/' . $class . '.php';
    }
);


require_once 'functions/sanitize.php';

// check if user is remembered but there is no user id exists in current sesion - not logged in
if (Cookie::Exists(Config::Get('remember/cookie_name')) && !Session::Exists(Config::Get('session/session_name')))
{
    /* automatically log user in */

    // get the value of hash in cookie
    $hash = Cookie::Get(Config::Get('remember/cookie_name'));

    // get user id has this hash value in the database
    $db = DB::GetInstance();
    $db->Get('users_session', array('hash', '=', $hash));

    // check if found any qualified user id
    if ($db->Count())
    {
        // hash matched, log user in
        $user = new User($db->First()->user_id);
    }
    else
    {
        // hash stored in the cookie is invalid, delete it
        Cookie::Delete(Config::Get('remember/cookie_name'));
    }
}
?>
