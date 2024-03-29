<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class NotaStorage
{
    protected $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function inserirNota($nota)
    {
        $save = $this->db->prepare("INSERT INTO nota_por_aluno (aluno, disciplina, turma, nota1, nota2, nota3, nota4, rec1, rec2, rec3, rec4) VALUES (:idAluno, :idDisciplina, :idTurma, 0, 0, 0, 0, 0, 0, 0, 0)");
        $save->execute($nota);
    }

    public function adicionarNota($data)
    {
        $aluno = $data['aluno'];
        $turma = $data['turma'];
        $disciplina = $data['disciplina'];
        $notaNum = $data['tipo'];
        $nota = $data['nota'];

        $user = $this->db->prepare("UPDATE nota_por_aluno SET ".$notaNum."=:nota WHERE aluno=:idAluno AND disciplina=:idDisciplina AND turma=:idTurma
        ");

        $user->execute([
            'nota' => $nota,
            'idAluno' => $aluno,
            'idDisciplina' => $disciplina,
            'idTurma' => $turma,
        ]);
    }

    public function verTurmasComNotaDoAluno($aluno)
    {
        $exec = $this->db->query("
            SELECT npa.turma, CONCAT(t.nome, ' (', t.ano, ')') AS nome_turma, u.nome
            FROM nota_por_aluno npa
            JOIN turma t ON npa.turma = t.id
            JOIN aluno a ON a.id = npa.aluno 
            JOIN usuario u ON u.id = a.usuario 
            WHERE aluno=$aluno
            GROUP BY turma
        ");

        return $exec->fetchAll(PDO::FETCH_OBJ);
    }

    public function verNotasPorTruma($aluno, $turma)
    {
        $exec = $this->db->query("
            SELECT npa.*, CONCAT('Prof. ',IFNULL(u.nome, 'INDEFINIDO')) AS nome_professor, IFNULL(p.id, 'ID indefinido') AS professor, d.nome AS materia, CONCAT(t.ano, ' ', t.nome) as turma
            FROM nota_por_aluno npa
            JOIN disciplina d ON d.id = npa.disciplina
            JOIN turma t ON t.id = npa.turma
            LEFT JOIN disciplina_por_professor dpp ON dpp.disciplina = d.id AND dpp.turma = t.id
            LEFT JOIN professor p ON p.id = dpp.professor
            LEFT JOIN usuario u ON u.id = p.usuario
            WHERE npa.aluno = $aluno
            AND npa.turma = $turma
            ORDER BY npa.disciplina
        ");

        return $exec->fetchAll(PDO::FETCH_OBJ);
    }

    public function verTurmasEMateriasComNotasDoAluno($aluno)
    {
        $alunosQuery = $this->db->query("
            SELECT npa.turma, CONCAT(t.ano, ' ', t.nome) AS nome_da_turma
            FROM nota_por_aluno npa
            JOIN turma t ON npa.turma = t.id
            WHERE aluno=$aluno
            GROUP BY turma
        ");

        return $alunosQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verNotasPorAlunosDaDisciplinaETurma($disciplina, $turma)
    {
        $alunosQuery = $this->db->query("
            SELECT usuario.id, usuario.nome, aluno.id as aluno, nota_por_aluno.nota1, nota_por_aluno.nota2, nota_por_aluno.nota3, nota_por_aluno.nota4, nota_por_aluno.rec1, nota_por_aluno.rec2, nota_por_aluno.rec3, nota_por_aluno.rec4
            FROM usuario
            inner join aluno on aluno.usuario = usuario.id
            inner join nota_por_aluno on nota_por_aluno.aluno = aluno.id AND nota_por_aluno.disciplina=$disciplina AND nota_por_aluno.turma=$turma
            order by usuario.nome
        ");

        return $alunosQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function verNotasPorAluno($aluno)
    {
        $alunosQuery = $this->db->query("
            SELECT *
            FROM nota_por_aluno
            WHERE aluno = $aluno
        ");

        return $alunosQuery->fetchAll(PDO::FETCH_OBJ);
    }
}
