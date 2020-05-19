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
    }
    
    public function verAlunosDaTurma($turma)
    {
        $alunosQuery = $this->db->query("SELECT * FROM aluno where turma = $turma");
        return $alunosQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function pegarTurmaDoAlunoPorTurma($id)
    {
        $turmaQuery = $this->db->query("select * from turma where id = $id");
        $turmaQuery = $turmaQuery->fetchObject();

        return $turmaQuery->serie.'º Série '.$turmaQuery->nome;
    }

    public function pegarTurmaDoAlunoPorUsuario($idUsuario)
    {
        $alunoQuery = $this->db->query("
            select * from aluno where usuario = $idUsuario
        ");
        $alunoQuery = $alunoQuery->fetchObject();

        $turmaQuery = $this->db->query("
            select * from turma where id = $alunoQuery->turma
        ");
        $turmaQuery = $turmaQuery->fetchObject();

        $nomeTurma = 'Sem turma';
        if ($turmaQuery) {
            $nomeTurma = " na " . $turmaQuery->serie.'º Série '.$turmaQuery->nome;
        }

        return $nomeTurma;
    }
}
