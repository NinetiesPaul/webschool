<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class NotaStorage extends DB
{
    public function inserirNota($nota)
    {
        $save = $this->connect()->prepare("INSERT INTO nota_por_aluno (aluno, disciplina, turma, nota1, nota2, nota3, nota4, rec1, rec2, rec3, rec4) VALUES (:idAluno, :idDisciplina, :idTurma, 0, 0, 0, 0, 0, 0, 0, 0)");
        $save->execute($nota);
    }
}
