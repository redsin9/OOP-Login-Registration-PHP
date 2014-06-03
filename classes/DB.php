<?php

class DB
{
    private static $mInstance = NULL;
    private $mPDO, 
            $mQuery,
            $mError = false, 
            $mResults, 
            $mCount = 0;


    private function __construct()
    {
        try
        {
            $this->mPDO = new PDO
            (
                'mysql:host=' . Config::Get('mysql/host') . // host
                ';dbname=' . Config::Get('mysql/db'),       // database name
                Config::Get('mysql/username'),              // user name
                Config::Get('mysql/password')               // password
            );
        }
        catch (PDOException $e)
        {
            die ($e->getMessage());
        }
    }



    public static function GetInstance()
    {
        // if singleton doesn't exist, instantiate new one
        if (!isset(self::$mInstance))
        {
            self::$mInstance = new DB();
        }

        // return singleton
        return self::$mInstance;
    }



    public function Query($sql, $params = array())
    {
        $this->mError = false;

        if ($this->mQuery = $this->mPDO->prepare($sql))
        {
            // bind extra params
            if (count($params))
            {
                // bind all extra params to the corresponding token
                $i = 1;
                foreach ($params as $param) 
                {
                    $this->mQuery->bindValue($i, $param);
                    $i++;
                }                
            }

            // execute the query
            if ($this->mQuery->execute())
            {
                // get the result as an object
                $this->mResults = $this->mQuery->fetchAll(PDO::FETCH_OBJ);

                // count the number of rows
                $this->mCount = $this->mQuery->rowCount();
            }
            else
            {
                $this->mError = true;
            }
        }
    }



    public function Action($action, $table, $where = array())
    {
        $result = true;


        // create query string
        $sql = "{$action} FROM {$table}";


        // check if there is where clause
        if (count($where) === 3)
        {
            $operators = array('=', '>', '<', '>=', '<=');

            $field      = $where[0];
            $operator   = $where[1];
            $value      = $where[2];

            // check if operator is in the defined array above
            if (in_array($operator, $operators))
            {
                // append WHERE clause
                $sql .= " WHERE {$field} {$operator} ?";

                // execute the query with where clause
                $this->Query($sql, array($value));                
            }
            else
            {
                $result = false;
            }
        }
        else
        {
            // execute the query
            $this->Query($sql);
        }
        
        // check if queried successfully
        if ($this->Error())
        {
            $result = false;
        }       

        return $result;
    }



    public function Get($table, $where)
    {
        $this->Action("SELECT *", $table, $where);
        return $this->mResults;
    }



    public function Delete($table, $where)
    {
        return $this->Action('DELETE', $table, $where);
    }



    public function Insert($table, $fields = array())
    {
        $result = true;


        // make sure there is data to insert into database
        if (count($fields))
        {
            $keys = array_keys ($fields);
            $values = "";
            $i = 1;

            // create a list of token for values
            foreach ($fields as $field)
            {
                $values .= '?';                

                if($i < count($fields))
                {
                    $values .= ', ';
                }
                $i++;
            }

            // prepare the query
            $sql = "INSERT INTO {$table} (`" . implode("`, `", $keys) . "`) VALUES ({$values})";
            
            // execute the query
            $this->Query($sql, $fields);
            if ($this->Error())
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



    public function Update($table, $id, $fields = array())
    {
        $result = true;


        // make sure there is new data to update
        if (count($fields))
        {
            $set = '';
            $i = 1;


            foreach ($fields as $name => $value) 
            {
                $set .= "{$name} = ?";

                // put seperator ',' between each field
                if ($i < count($fields))
                {
                    $set .= ', ';
                }
                $i++;
            }

            $sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";

            // execute the query
            $this->Query($sql, $fields);

            // check error
            if ($this->Error())
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



    // SQL object contains result from query
    public function Results()
    {
        return $this->mResults;
    }



    // return first record from query
    public function First()
    {
        return $this->Results()[0];
    }



    // bool
    public function Error()
    {
        return $this->mError;
    }



    // int
    public function Count()
    {
        return $this->mCount;
    }
}



?>

