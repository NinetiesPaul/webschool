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
        $turmas = $this->db->query("
            SELECT *
            FROM turma
            ORDER BY ano
        ");

        return $turmas->fetchAll(PDO::FETCH_OBJ);
    }

    public function verTurma($turma)
    {
        $turma = $this->db->query("
            SELECT *
            FROM turma
            WHERE id = $turma
        ");

        return $turma->fetch(PDO::FETCH_OBJ);
    }

    public function adicionarTurma($nome, $ano)
    {
        $user = $this->db->prepare("INSERT INTO turma (nome, ano) VALUES (:nome, :ano)");

        $user->execute([
            'nome' => $nome,
            'ano' => $ano,
        ]);
    }

    public function alterarTurma($nome, $ano, $turma)
    {
        $user = $this->db->prepare("UPDATE turma SET ano=:ano, nome=:nome WHERE id=:turma");

        $user->execute([
            'nome' => $nome,
            'ano' => $ano,
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
            $this->throwError("<b>Erro</b>: id inválido");
        }
    }
    
    public function verAlunosDaTurma($turma)
    {
        $alunosQuery = $this->db->query("
            SELECT *
            FROM aluno
            WHERE turma = $turma
        ");

        return $alunosQuery->fetchAll(PDO::FETCH_OBJ);
    }

    private function throwError($msg)
    {
        throw new \Exception($msg);
    }
}
