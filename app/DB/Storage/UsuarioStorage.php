<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class UsuarioStorage
{
    protected $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function inserirUsuario($usuario)
    {
        $user = $this->db->prepare("
            INSERT INTO usuario (nome, email, pass, endereco, salt)
            VALUES (:name, :email, :password, :endereco, :salt)
        ");

        $user->execute($usuario);
        
        return (int) $this->db->lastInsertId();
    }
}
