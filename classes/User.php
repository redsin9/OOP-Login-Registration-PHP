<?php

class User
{
    private $mDB;
    private $mData;
    private $mSessionName;
    private $mCookieName;
    private $mLoggedIn;



    public function __construct($user = null)
    {
        $this->mDB = DB::GetInstance();
        $this->mSessionName = Config::Get('session/session_name');
        $this->mCookieName = Config::Get('remember/cookie_name');


        // if user has not been defined - try to get the current logged in user
        if ($user == null)
        {
            // check there is session for user at the moment
            if (Session::Exists($this->mSessionName))
            {
                // check if found user exists in database
                if ($this->Find(Session::Get($this->mSessionName)))
                {
                    $this->mLoggedIn = true;
                }
                else
                {
                    $this->Logout();
                }
            }
        }
        else
        {
            // try to find the user
            if ($this->Find($user))
            {
                // check if found user currently logged in by comparing user id with session id
                if (Session::Exists($this->mSessionName) && $this->mData->id == Session::Get($this->mSessionName))
                {
                    $this->mLoggedIn = true;
                }
            }
        }
    }



    public function Update ($fields = array(), $id = null)
    {
        // if id is not specified, get it from the data of current logged in user
        if (!$id && $this->IsLoggedIn())
        {
            $id = $this->mData->id;
        }


        if (!$this->mDB->Update('users', $id, $fields))
        {
            throw new Exception('There was a problem updating.');
        }
    }



    public function Create($fields = array())
    {
        if ($this->mDB->Insert('users', $fields) == false)
        {
            throw new Exception('There was a problem creating an account.');
        }
    }



    private function Find ($user = null)
    {
        $result = true;

        if ($user)
        {
            // detect the passed parameter is for id or username
            $field = (is_numeric($user)) ? 'id' : 'username';
            $this->mDB->Get('users', array($field, '=', $user));

            if ($this->mDB->Count())
            {
                // get the data about the user
                $this->mData = $this->mDB->First();
            }
            else
            {
                $result = false;
            }
        }
        else
        {
            $result = false;
        }

        return $result;
    }



    public function Login ($username = null, $password = null, $remember = false)
    {
        $result = true;


        // make sure username and password is not null
        if ($username == null || $password == null)
        {
            // check if user exists
            if ($this->Exists())
            {
                // log the user in by save user id in the session
                Session::Put($this->mSessionName, $this->mData->id);
            }
            else
            {
                $result = false;
            }
        }
        else
        {
            // check if this user exists
            if ($this->Find($username))
            {
                // check password
                if ($this->mData->password === Hash::Make($password, $this->mData->salt))
                {
                    // store user id within session
                    Session::Put($this->mSessionName, $this->mData->id);

                    // remember user
                    if ($remember)
                    {
                        $hash = null;

                        // check if there is already hash stored in the database
                        $this->mDB->Get('users_session', array('user_id', '=', $this->mData->id));
                        if ($this->mDB->Count() <= 0)
                        {
                            // if not, insert new one to the database
                            $hash = Hash::Unique();      // generate a unique hash
                            $this->mDB->Insert
                            (
                                'users_session',
                                array
                                (
                                    'user_id' => $this->mData->id,
                                    'hash' => $hash
                                )
                            );
                        }
                        else
                        {
                            // get the hash value from database for this user session
                            $hash = $this->mDB->First()->hash;
                        }

                        // store this hash in the cookie - client side
                        Cookie::Put($this->mCookieName, $hash, Config::Get('remember/cookie_expiry'));
                    }
                }
                else
                {
                    $result = false;
                }
            }
            else
            {
                $result =false;
            }
        }

        return $result;
    }



    public function Exists()
    {
        $result = true;


        // if data about the user is empty, so user does not exist
        if (empty($this->mData))
        {
            $result = false;
        }

        return $result;
    }



    public function Logout ()
    {
        $this->mDB->Delete('users_session', array('user_id', '=', $this->mData->id));
        Session::Delete($this->mSessionName);
        Cookie::Delete($this->mCookieName);
    }



    public function Data()
    {
        return $this->mData;
    }



    public function IsLoggedIn()
    {
        return $this->mLoggedIn;
    }



    public function HasPermission($key)
    {
        $result = true;


        // get data group of this user based on group id
        $this->mDB->Get('groups', array('id', '=', $this->mData->group));

        // check if user is in a group
        if ($this->mDB->Count())
        {
            // extract permissions of the group
            $permissions = json_decode($this->mDB->First()->permissions, true);

            // check if user has the required permission
            if ($permissions[$key] == false)
            {
                $result = false;
            }
        }
        else
        {
            $result = false;
        }

        return $result;
    }
}



?>