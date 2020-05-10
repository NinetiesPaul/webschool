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

    public function verTurmasComNotaDoAluno($aluno)
    {
        $exec = $this->connect()->query("select turma from nota_por_aluno where aluno=$aluno group by turma");
        return $exec->fetchAll(PDO::FETCH_OBJ);
    }

    public function verNotasPorTruma($aluno, $turma)
    {
        $exec = $this->connect()->query("select * from nota_por_aluno where aluno=$aluno and turma=$turma order by disciplina");
        return $exec->fetchAll(PDO::FETCH_OBJ);
    }

    public function verTurmasdoAluno($idAluno)
    {
        $exec = $this->connect()->query("select turma from nota_por_aluno where aluno=$idAluno group by turma");
        return $exec->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function adicionarNota($data)
    {
        $aluno = $data['aluno'];
        $turma = $data['turma'];
        $disciplina = $data['disciplina'];
        $notaNum = $data['tipo'];
        $nota = $data['nota'];
                
        $user = $this->connect()->prepare("
            UPDATE nota_por_aluno
            SET ".$notaNum."=:nota
            where aluno=:idAluno and disciplina=:idDisciplina and turma=:idTurma
        ");

        $user->execute([
            'nota' => $nota,
            'idAluno' => $aluno,
            'idDisciplina' => $disciplina,
            'idTurma' => $turma,
        ]);
    }

    public function verNotasPorAlunosDaDisciplinaETurma($disciplina, $turma)
    {
        $alunosQuery = $this->connect()->query("
            select usuario.id, usuario.nome, aluno.id as aluno, nota_por_aluno.nota1, nota_por_aluno.nota2, nota_por_aluno.nota3, nota_por_aluno.nota4, nota_por_aluno.rec1, nota_por_aluno.rec2, nota_por_aluno.rec3, nota_por_aluno.rec4
            from usuario
            inner join aluno on aluno.usuario = usuario.id
            inner join nota_por_aluno on nota_por_aluno.aluno = aluno.id and nota_por_aluno.disciplina=$disciplina and nota_por_aluno.turma=$turma
            group by usuario.nome
            order by usuario.nome
        ");
        return $alunosQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function verNotasPorAluno($aluno)
    {
        $alunosQuery = $this->connect()->query("
            select * from nota_por_aluno where aluno = $aluno
        ");
        return $alunosQuery->fetchAll(PDO::FETCH_OBJ);
    }
}
