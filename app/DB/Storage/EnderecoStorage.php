<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class EnderecoStorage
{
    public $db;
    
    public function __construct()
    {
        $this->db = new DB();
    }
    
    public function inserirEndereco()
    {
        $endereco = $this->db->prepare("INSERT INTO endereco (estado) VALUES (:estado)");

        $endereco->execute([
            'estado' => 1,
        ]);

        return (int) $this->db->lastInsertId();
    }
    
    public function verEndereco($id)
    {
        $enderecoQuery = $this->db->query("select * from endereco where id = $id");
        return $enderecoQuery->fetch(PDO::FETCH_OBJ);
    }

    public function pegarEstadoPeloEstado($id)
    {
        $estadoQuery = $this->db->query("select * from estado where id=$id");
        $estado = $estadoQuery->fetchObject();

        return $estado->nome.', '.$estado->sigla;
    }
}
