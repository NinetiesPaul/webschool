<?php

namespace App\DB;

use PDO;

class DB extends PDO
{
    protected $conn;
    
    public function __construct()
    {
        $localhost_db = getenv('DB_HOST');
        $dbname_db = getenv('DB_NAME');
        $user_db = getenv('DB_USER');
        $password_db = getenv('DB_PASSWORD');
        parent::__construct("mysql:host=$localhost_db; dbname=$dbname_db", $user_db, $password_db, []);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function connect()
    {
        return new DB();
    }
}
