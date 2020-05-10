<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class EnderecoStorage extends DB
{
    public $localConnection;
    
    public function __construct()
    {
        parent::__construct();
        $this->localConnection = $this->connect();
    }
    
    public function inserirEndereco()
    {
        $endereco = $this->localConnection->prepare("INSERT INTO endereco (estado) VALUES (:estado)");

        $endereco->execute([
            'estado' => 1,
        ]);

        return (int) $this->localConnection->lastInsertId();
    }
    
    public function verEndereco($id)
    {
        $enderecoQuery = $this->connect()->query("select * from endereco where id = $id");
        return $enderecoQuery->fetch(PDO::FETCH_OBJ);
    }
}
