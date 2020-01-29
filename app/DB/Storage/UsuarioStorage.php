<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class UsuarioStorage extends DB
{    
    public $connection;
    
    public function __construct() {
        parent::__construct();
        $this->connection = $this->connect();
    }

    public function inserirUsuario($usuario)
    {
        $user = $this->connection->prepare("
            INSERT INTO usuario (nome, email, pass, endereco, salt)
            VALUES (:name, :email, :password, :endereco, :salt)
        ");

        $user->execute($usuario);
        
        return (int) $this->connection->lastInsertId();
    }
}
