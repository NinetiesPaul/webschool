<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class ArquivoStorage extends DB
{    
    public $connection;
    
    public function __construct() {
        parent::__construct();
        $this->connection = $this->connect();
    }
    
    public function adicionarArquivo($file_name, $urlThumbFinal, $urlFinal, $dataComentario, $id)
    {
        $fileQuery = $this->connection->prepare("
            INSERT INTO arquivos (nome, endereco_thumb, endereco, contexto, diario, descricao, data) VALUES (:nome, :endereco_thumb, :endereco, 'ddc', $id, '', :data)
        ");

        $fileQuery->execute([
            'nome' => $file_name,
            'endereco_thumb' => $urlThumbFinal,
            'endereco' => $urlFinal,
            'data' => $dataComentario,
        ]);
        
        return $this->connection->lastInsertId();
    }

    public function verArquivosDoDiario($comentario)
    {
        $arquivoQuery = $this->connection->query("
            select *
            from arquivos
            where contexto = 'ddc'
            and diario = $comentario
        ");
        return $arquivoQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verArquivoDoDiario($comentario)
    {
        $arquivoQuery = $this->connection->query("
            select *
            from arquivos
            where contexto = 'ddc'
            and diario = $comentario
        ");
        return $arquivoQuery->fetch(PDO::FETCH_OBJ);
    }
    
    public function verEnderecoNomeDoArquivo($id_arquivo)
    {           
        $arquivoQuery = $this->connect()->query("
            select endereco, nome
            from arquivos
            where id = $id_arquivo
        ");
        
        return $arquivoQuery->fetch(PDO::FETCH_OBJ);
    }
    
    public function removerArquivoDoComentario($arquivo)
    {
        $statement = $this->connect()->prepare("DELETE FROM arquivos where id=:id");
        $statement->execute([
            'id' => $arquivo,
        ]);
    }
    
    public function verArquivoPorId($idArquivo)
    {
        $arquivoQuery = $this->connect()->query("
        select *
        from arquivos
        where id = $idArquivo
        ");
        return $arquivoQuery->fetch(PDO::FETCH_OBJ);
    }
}
