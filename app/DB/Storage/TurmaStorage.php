<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class TurmaStorage extends DB
{
    public function verTurmas()
    {
        $turmas = $this->connect()->query("select * from turma order by serie");
        return $turmas->fetchAll(PDO::FETCH_OBJ);
    }

    public function verTurma(int $turma)
    {
        $turma = $this->connect()->query("select * from turma where id = $turma");
        return $turma->fetch(PDO::FETCH_OBJ);
    }

    public function adicionarTurma($nome, $serie)
    {
        $user = $this->connect()->prepare("INSERT INTO turma (nome, serie)
                VALUES (:nome, :serie)");

        $user->execute([
            'nome' => $nome,
            'serie' => $serie,
        ]);
    }

    public function alterarTurma($nome, $serie, $turma)
    {
        $user = $this->connect()->prepare("
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
        $user = $this->connect()->prepare("DELETE FROM turma WHERE id=:id");

        $user->execute([
            'id' => $turma,
        ]);
    }
    
    public function verAlunosDaTurma($turma)
    {
        $alunosQuery = $this->connect()->query("SELECT * FROM aluno where turma = $turma");
        return $alunosQuery->fetchAll(PDO::FETCH_OBJ);
    }
}
