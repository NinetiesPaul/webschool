<?php

namespace App\DB;

use PDO;

class DB extends PDO {
    
    protected $conn;
    
    public function __construct()
    {
        $localhost_db = 'localhost';
        $dbname_db = 'webschool';
        $user_db = 'root';
        $password_db = '';
        parent::__construct("mysql:host=$localhost_db; dbname=$dbname_db", $user_db, $password_db, []);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
