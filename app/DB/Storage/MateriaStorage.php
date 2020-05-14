<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class MateriaStorage extends DB
{
    public function verMaterias()
    {
        $disciplinaQuery = $this->connect()->query("select * from disciplina");
        return $disciplinaQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verMateria(int $materia)
    {
        $disciplinaQuery = $this->connect()->query("select * from disciplina where id = $materia");
        return $disciplinaQuery->fetch(PDO::FETCH_OBJ);
    }

    public function adicionarMateria($nome)
    {
        $user = $this->connect()->prepare("INSERT INTO disciplina (nome)
                VALUES (:nome)");

        $user->execute([
            'nome' => $nome,
        ]);
    }

    public function alterarMateria($nome, $id)
    {
        $user = $this->connect()->prepare("
            UPDATE disciplina
            SET nome=:nome
            where id=:disciplina
            ");

        $user->execute([
            'nome' => $nome,
            'disciplina' => $id,
        ]);
    }

    public function removerMateria($materia)
    {
        $user = $this->connect()->prepare("DELETE FROM disciplina WHERE id=:id");

        $user->execute([
            'id' => $materia,
        ]);
    }
    
    public function verMateriaPorProfessor()
    {
        $disciplinasQuery = $this->connect()->query("select * from disciplina_por_professor order by turma");
        return $disciplinasQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function verMateriaPorProfessorPorTurma($turma)
    {
        $disciplinasQuery = $this->connect()->query("SELECT * FROM disciplina_por_professor where turma = $turma");
        return $disciplinasQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verMateriaPorProfessorDoProfessor($professor)
    {
        $disciplinaQuery = $this->connect()->query("
            SELECT disciplina_por_professor.*, disciplina.nome as nomeDisciplina, turma.serie, turma.nome FROM disciplina_por_professor
            INNER JOIN disciplina ON disciplina.id = disciplina_por_professor.disciplina
            INNER JOIN turma ON turma.id = disciplina_por_professor.turma
            WHERE professor=$professor
        ");
        return $disciplinaQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verMateriaPorProfessorSingle($id)
    {
        $disciplinaQuery = $this->connect()->query("
            SELECT disciplina_por_professor.*, disciplina.nome as nomeDisciplina, turma.serie, turma.nome FROM disciplina_por_professor
            INNER JOIN disciplina ON disciplina.id = disciplina_por_professor.disciplina
            INNER JOIN turma ON turma.id = disciplina_por_professor.turma
            WHERE disciplina_por_professor.id=$id
        ");
        return $disciplinaQuery->fetchObject();
    }

    public function pegarNomeDaDisciplina(int $id)
    {
        $disciplinaQuery = $this->connect()->query("select nome from disciplina where id = $id");
        $disciplina = $disciplinaQuery->fetchObject();

        return $disciplina->nome;
    }
}
