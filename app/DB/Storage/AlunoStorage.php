<?php


namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class AlunoStorage extends DB
{
    public function verAlunos()
    {
        $alunoQuery = $this->connect()->query("select usuario.* from usuario, aluno where usuario.id=aluno.usuario");
        return $alunoQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verAluno(int $aluno)
    {
        $alunoQuery = $this->connect()->query("select usuario.* from usuario, aluno where usuario.id=aluno.usuario and aluno.usuario = $aluno");
        return $alunoQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function adicionarAluno()
    {

    }

    public function alterarAluno()
    {

    }

    public function removerAluno()
    {

    }
}
