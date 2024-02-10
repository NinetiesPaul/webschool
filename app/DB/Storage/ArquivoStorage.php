<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class ArquivoStorage
{
    public $db;
    
    public function __construct()
    {
        $this->db = new DB();
    }
    
    public function adicionarArquivo($file_name, $urlThumbFinal, $urlFinal, $dataComentario, $id)
    {
        $fileQuery = $this->db->prepare("
            INSERT INTO arquivos (nome, endereco_thumb, endereco, contexto, diario, descricao, data) VALUES (:nome, :endereco_thumb, :endereco, 'ddc', $id, '', :data)
        ");

        $fileQuery->execute([
            'nome' => $file_name,
            'endereco_thumb' => $urlThumbFinal,
            'endereco' => $urlFinal,
            'data' => $dataComentario,
        ]);
        
        return $this->db->lastInsertId();
    }

    public function verArquivosDoDiario($comentario)
    {
        $arquivoQuery = $this->db->query("
            SELECT *
            FROM arquivos
            WHERE contexto = 'ddc'
            AND diario = $comentario
        ");

        return $arquivoQuery->fetch(PDO::FETCH_OBJ);
    }

    public function verArquivoDoDiario($comentario)
    {
        $arquivoQuery = $this->db->query("
            SELECT *
            FROM arquivos
            WHERE contexto = 'ddc'
            AND diario = $comentario
        ");

        return $arquivoQuery->fetch(PDO::FETCH_OBJ);
    }
    
    public function removerArquivoDoComentario($arquivo)
    {
        $statement = $this->db->prepare("DELETE FROM arquivos WHERE id=:id");
        $statement->execute([
            'id' => $arquivo,
        ]);
    }
    
    public function verArquivoPorId($idArquivo)
    {
        $arquivoQuery = $this->db->query("
            SELECT *
            FROM arquivos
            WHERE id = $idArquivo
        ");

        return $arquivoQuery->fetch(PDO::FETCH_OBJ);
    }
    
    public function verArquivoPorAluno($aluno)
    {
        $arquivosQuery = $this->db->query("
            SELECT *
            FROM arquivos
            WHERE diario in (SELECT id FROM diario_de_classe WHERE aluno = $aluno AND contexto = 'observacao')
        ");

        return $arquivosQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function verArquivoPorProfessor($professor)
    {
        $arquivosQuery = $this->db->query("
            SELECT *
            FROM arquivos
            WHERE diario in (SELECT id FROM diario_de_classe WHERE professor = $professor AND contexto = 'observacao')
        ");

        return $arquivosQuery->fetchAll(PDO::FETCH_OBJ);
    }
}
