<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class EnderecoStorage extends DB
{
    public function __construct(?PDO $db = null)
    {
        parent::__construct($db);
    }

    public function inserirEndereco()
    {
        $endereco = $this->db->prepare("INSERT INTO endereco (estado) VALUES (:estado)");

        $endereco->execute([
            'estado' => 1,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function atualizarEndereco($rua, $numero, $bairro, $complemento, $cidade, $cep, $estado, $endereco)
    {
        $user = $this->db->prepare("
            UPDATE endereco
            SET rua=:rua, numero=:numero, bairro=:bairro, complemento=:complemento, cidade=:cidade, cep=:cep, estado=:estado
            WHERE id=:endereco
        ");

        $user->execute([
            'rua' => $rua,
            'numero' => $numero,
            'bairro' => $bairro,
            'complemento' => $complemento,
            'cidade' => $cidade,
            'cep' => $cep,
            'estado' => $estado,
            'endereco' => $endereco,
        ]);
    }
    
    public function verEndereco($id)
    {
        $enderecoQuery = $this->db->query("
            SELECT *
            FROM endereco
            WHERE id = $id
        ");

        return $enderecoQuery->fetch(PDO::FETCH_OBJ);
    }

    public function pegarEstadoPeloEstado($id)
    {
        $estadoQuery = $this->db->query("
            SELECT *
            FROM estado
            WHERE id=$id
        ");
        $estado = $estadoQuery->fetch(PDO::FETCH_OBJ);;

        return $estado->nome.', '.$estado->sigla;
    }

    public function pegarEstados()
    {
        $estadoQuery = $this->db->query("
            SELECT *
            FROM estado
            ORDER BY nome
        ");
        return $estadoQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function deletarEndereco($usuario, $endereco)
    {
        $user = $this->db->prepare("UPDATE usuario SET endereco = NULL WHERE id = :id;");
        $user->execute([
            'id' => $usuario,
        ]);

        $user = $this->db->prepare("DELETE FROM endereco WHERE id = :endereco;");
        $user->execute([
            'endereco' => $endereco,
        ]);
    }
}
