<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class MateriaStorage
{
    protected $db;
    
    public function __construct()
    {
        $this->db = new DB();
    }

    public function verMaterias()
    {
        $disciplinaQuery = $this->db->query("
            SELECT *
            FROM disciplina
        ");

        return $disciplinaQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verMateria($materia)
    {
        $disciplinaQuery = $this->db->query("
            SELECT *
            FROM disciplina
            WHERE id = $materia
        ");

        return $disciplinaQuery->fetch(PDO::FETCH_OBJ);
    }

    public function adicionarMateria($nome)
    {
        $user = $this->db->prepare("INSERT INTO disciplina (nome)
                VALUES (:nome)");

        $user->execute([
            'nome' => $nome,
        ]);
    }

    public function alterarMateria($nome, $id)
    {
        $user = $this->db->prepare("UPDATE disciplina SET nome=:nome WHERE id=:disciplina");

        $user->execute([
            'nome' => $nome,
            'disciplina' => $id,
        ]);
    }

    public function removerMateria($materia)
    {
        $user = $this->db->prepare("DELETE FROM disciplina WHERE id=:id");

        $user->execute([
            'id' => $materia,
        ]);

        if ($user->rowCount() === 0) {
            $this->throwError("<b>Erro</b>: id inválido");
        }
    }
    
    public function verMateriasPorProfessor()
    {
        $disciplinasQuery = $this->db->query("
            SELECT *
            FROM disciplina_por_professor
            ORDER BY turma
        ");

        return $disciplinasQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function verMateriaPorProfessorPorTurma($turma)
    {
        $disciplinasQuery = $this->db->query("
            SELECT *
            FROM disciplina_por_professor
            WHERE turma = $turma
        ");

        return $disciplinasQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function verMateriaPorProfessorPorId($id)
    {
        $disciplinasQuery = $this->db->query("
            SELECT *
            FROM disciplina_por_professor
            WHERE id = $id
        ");

        return $disciplinasQuery->fetchObject();
    }

    public function verMateriasDoProfessor($professor)
    {
        $disciplinaQuery = $this->db->query("
            SELECT disciplina_por_professor.*, disciplina.nome AS nomeDisciplina, turma.ano, turma.nome FROM disciplina_por_professor
            INNER JOIN disciplina ON disciplina.id = disciplina_por_professor.disciplina
            INNER JOIN turma ON turma.id = disciplina_por_professor.turma
            WHERE professor=$professor
        ");
        return $disciplinaQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verMateriasDoProfessorAdmin($professor)
    {
        $disciplinaQuery = $this->db->query("
            SELECT dpp.id, CONCAT(t.nome, ' (', t.ano, ')') AS turma, d.nome FROM disciplina_por_professor dpp
            JOIN disciplina d on d.id = dpp.disciplina 
            JOIN turma t on t.id = dpp.turma 
            WHERE dpp.professor = $professor
            ORDER BY turma;
        ");
        return $disciplinaQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verMateriaDoProfessor($id)
    {
        $disciplinaQuery = $this->db->query("
            SELECT disciplina_por_professor.*, disciplina.nome AS nomeDisciplina, turma.ano, turma.nome FROM disciplina_por_professor
            INNER JOIN disciplina ON disciplina.id = disciplina_por_professor.disciplina
            INNER JOIN turma ON turma.id = disciplina_por_professor.turma
            WHERE disciplina_por_professor.id=$id
        ");
        return $disciplinaQuery->fetchObject();
    }

    private function throwError($msg)
    {
        throw new \Exception($msg);
    }
}
