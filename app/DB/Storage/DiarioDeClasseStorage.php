<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class DiarioDeClasseStorage
{
    public $db;
    
    public function __construct()
    {
        $this->db = new DB();
    }
    
    public function inserirDiarioDeClasse($diario)
    {
        $save = $this->db->prepare("INSERT INTO diario_de_classe (aluno, disciplina, turma, data, contexto, presenca) VALUES (:idAluno, :idDisciplina, :idTurma, NOW(), 'presenca', 0)");
        $save->execute($diario);
    }
    
    public function verFaltasDoAlunoDaTurma($turma, $disc)
    {
        $alunosQuery = $this->db->query("
            SELECT diario_de_classe.aluno, diario_de_classe.disciplina, diario_de_classe.turma, usuario.nome
            FROM usuario
            INNER JOIN aluno ON aluno.usuario = usuario.id
            INNER JOIN diario_de_classe ON diario_de_classe.aluno = aluno.id
            AND diario_de_classe.disciplina = $disc AND diario_de_classe.turma = $turma
            GROUP BY diario_de_classe.aluno
            ORDER BY diario_de_classe.aluno 
        ");

        return $alunosQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function verFaltasDoAlunoDaturmaPorData($aluno, $turma, $disc, $data)
    {
        $diarioQuery = $this->db->query("
            SELECT *
            FROM diario_de_classe
            WHERE aluno = $aluno
            AND diario_de_classe.disciplina = $disc AND diario_de_classe.turma = $turma
            AND data = '".$data."'
        ");

        return $diarioQuery->fetch(PDO::FETCH_OBJ);
    }
    
    public function adicionarFalta($aluno, $turma, $disciplina, $data)
    {
        $user = $this->db->prepare("INSERT INTO diario_de_classe (aluno, disciplina, turma, data, presenca, contexto) values (:aluno, :disciplina, :turma, :data, 1, 'presenca')");

        $user->execute([
            'aluno' => $aluno,
            'disciplina' => $disciplina,
            'turma' => $turma,
            'data' => $data,
        ]);
    }
    
    public function alterarFalta($presenca, $diario)
    {
        $user = $this->db->prepare("UPDATE diario_de_classe SET presenca = :presenca WHERE id=:id");

        $user->execute([
            'presenca' => $presenca,
            'id' => $diario,
        ]);
    }
    
    public function verComentariosDoAlunoDaTurma($aluno, $disciplina, $turma, $data, $professor)
    {
        $comentarioQuery = $this->db->query("
            SELECT *
            FROM diario_de_classe
            WHERE aluno = $aluno
            AND disciplina = $disciplina
            AND turma = $turma
            AND data = '$data'
            AND professor = $professor
            AND contexto = 'observacao'
        ");

        return $comentarioQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function adicionarComentario($turma, $aluno, $disciplina, $mensagem, $data, $prof)
    {
        $user = $this->db->prepare("
            INSERT INTO diario_de_classe
            (turma, aluno, disciplina, observacao, data, professor, contexto) VALUES
            (:turma, :aluno, :disciplina, :mensagem, :data, :professor, 'observacao')
        ");

        $user->execute([
            'turma' => $turma,
            'aluno' => $aluno,
            'disciplina' => $disciplina,
            'mensagem' => $mensagem,
            'data' => $data,
            'professor' => $prof,
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function removerComentario($idComentario)
    {
        $comentario = $this->db->prepare("DELETE FROM diario_de_classe WHERE contexto = 'observacao' AND id=:id");
        $comentario->execute([
            'id' => $idComentario,
        ]);
    }
    
    public function verDiarioDeClassePorProfessor($professor)
    {
        $diarioQuery = $this->db->query("
            SELECT *
            FROM diario_de_classe
            WHERE professor = $professor
        ");

        return $diarioQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function verDiarioDeClassePorAluno($aluno)
    {
        $diarioQuery = $this->db->query("
            SELECT *
            FROM diario_de_classe
            WHERE aluno = $aluno
        ");

        return $diarioQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verFaltasPorAlunoDaMateriaETurma($turma, $aluno, $disciplina)
    {
        $faltasQuery = $this->db->query("
            SELECT *
            FROM diario_de_classe
            WHERE turma=$turma
            AND aluno=$aluno AND disciplina=$disciplina AND presenca = 1
            ORDER BY data
        ");

        return $faltasQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verComentariosPorAlunoDaMateriaETurma($turma, $aluno, $disciplina)
    {
        $comentariosQuery = $this->db->query("
            SELECT *
            FROM diario_de_classe
            WHERE turma=$turma AND aluno=$aluno AND disciplina=$disciplina AND contexto='observacao'
            ORDER BY data
        ");

        return $comentariosQuery->fetchAll(PDO::FETCH_OBJ);
    }
}
