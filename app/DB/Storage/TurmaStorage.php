<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class TurmaStorage
{
    protected $db;
    
    public function __construct()
    {
        $this->db = new DB();
    }

    public function verTurmas()
    {
        $turmas = $this->db->query("select * from turma order by serie");
        return $turmas->fetchAll(PDO::FETCH_OBJ);
    }

    public function verTurma($turma)
    {
        $turma = $this->db->query("select * from turma where id = $turma");
        return $turma->fetch(PDO::FETCH_OBJ);
    }

    public function adicionarTurma($nome, $serie)
    {
        $user = $this->db->prepare("INSERT INTO turma (nome, serie)
                VALUES (:nome, :serie)");

        $user->execute([
            'nome' => $nome,
            'serie' => $serie,
        ]);
    }

    public function alterarTurma($nome, $serie, $turma)
    {
        $user = $this->db->prepare("
            UPDATE turma
            SET serie=:serie, nome=:nome
            where id=:turma
            ");

        $user->execute([
            'nome' => $nome,
            'serie' => $serie,
            'turma' => $turma,
        ]);
    }

    public function removerTurma($turma)
    {
        $user = $this->db->prepare("DELETE FROM turma WHERE id=:id");

        $user->execute([
            'id' => $turma,
        ]);

        if ($user->rowCount() === 0) {
            $this->throwError("Erro ao recuperar turma: id inválido");
        }
    }
    
    public function verAlunosDaTurma($turma)
    {
        $alunosQuery = $this->db->query("SELECT * FROM aluno where turma = $turma");
        return $alunosQuery->fetchAll(PDO::FETCH_OBJ);
    }

    private function throwError($msg)
    {
        throw new \Exception($msg);
    }
}
