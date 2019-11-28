<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DB
 *
 * @author Paul Richard
 */

declare(strict_types=1);

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
        parent::__construct($dsn, $username, $password, $options);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
