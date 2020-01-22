<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class DiarioDeClasseStorage extends DB
{
    public function inserirDiarioDeClasse($diario)
    {
        $save = $this->connect()->prepare("INSERT INTO diario_de_classe (aluno, disciplina, turma, data, contexto, presenca) VALUES (:idAluno, :idDisciplina, :idTurma, NOW(), 'presenca', 0)");
        $save->execute($diario);
    }
}
