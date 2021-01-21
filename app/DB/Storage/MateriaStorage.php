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
        $disciplinaQuery = $this->db->query("select * from disciplina");
        return $disciplinaQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verMateria($materia)
    {
        $disciplinaQuery = $this->db->query("select * from disciplina where id = $materia");
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
        $user = $this->db->prepare("
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
        $user = $this->db->prepare("DELETE FROM disciplina WHERE id=:id");

        $user->execute([
            'id' => $materia,
        ]);

        if ($user->rowCount() === 0) {
            $this->throwError("Erro ao recuperar materia: id inválido");
        }
    }
    
    public function verMateriasPorProfessor()
    {
        $disciplinasQuery = $this->db->query("select * from disciplina_por_professor order by turma");
        return $disciplinasQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function verMateriaPorProfessorPorTurma($turma)
    {
        $disciplinasQuery = $this->db->query("SELECT * FROM disciplina_por_professor where turma = $turma");
        return $disciplinasQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verMateriasDoProfessor($professor)
    {
        $disciplinaQuery = $this->db->query("
            SELECT disciplina_por_professor.*, disciplina.nome as nomeDisciplina, turma.serie, turma.nome FROM disciplina_por_professor
            INNER JOIN disciplina ON disciplina.id = disciplina_por_professor.disciplina
            INNER JOIN turma ON turma.id = disciplina_por_professor.turma
            WHERE professor=$professor
        ");
        return $disciplinaQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verMateriasDoProfessorAdmin($professor)
    {
        $disciplinaQuery = $this->db->query("
            select dpp.id, CONCAT(t.serie, 'º Série ', t.nome) as turma, d.nome from disciplina_por_professor dpp
            join disciplina d on d.id = dpp.disciplina 
            join turma t on t.id = dpp.turma 
            where dpp.professor = $professor
            order by turma;
        ");
        return $disciplinaQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verMateriaDoProfessor($id)
    {
        $disciplinaQuery = $this->db->query("
            SELECT disciplina_por_professor.*, disciplina.nome as nomeDisciplina, turma.serie, turma.nome FROM disciplina_por_professor
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
