<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class DiarioDeClasseStorage extends DB
{    
    public $connection;
    
    public function __construct() {
        parent::__construct();
        $this->connection = $this->connect();
    }
    
    public function inserirDiarioDeClasse($diario)
    {
        $save = $this->connect()->prepare("INSERT INTO diario_de_classe (aluno, disciplina, turma, data, contexto, presenca) VALUES (:idAluno, :idDisciplina, :idTurma, NOW(), 'presenca', 0)");
        $save->execute($diario);
    }
    
    public function verFaltasDoAlunoDaTurma($turma, $disc)
    {
        $alunosQuery = $this->connect()->query("
            select diario_de_classe.*, usuario.nome
            from usuario
            inner join aluno on aluno.usuario = usuario.id
            inner join diario_de_classe on diario_de_classe.aluno = aluno.id
            and diario_de_classe.disciplina = $disc and diario_de_classe.turma = $turma
            group by usuario.nome
            order by usuario.nome 
        ");
        return $alunosQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function verFaltasDoAlunoDaturmaPorData($aluno, $turma, $disc, $data)
    {
        $diarioQuery = $this->connect()->query("
            select *
            from diario_de_classe
            where aluno = $aluno
            and diario_de_classe.disciplina = $disc and diario_de_classe.turma = $turma
            and data = '".$data."'
        ");
        return $diarioQuery->fetch(PDO::FETCH_OBJ);
    }
    
    public function adicionarFalta($aluno, $turma, $disciplina, $data)
    {
        $user = $this->connect()->prepare("
            INSERT INTO diario_de_classe (aluno, disciplina, turma, data, presenca, contexto) 
            values (:aluno, :disciplina, :turma, :data, 1, 'presenca')
        ");

        $user->execute([
            'aluno' => $aluno,
            'disciplina' => $disciplina,
            'turma' => $turma,
            'data' => $data,
        ]);
    }
    
    public function alterarFalta($presenca, $diario)
    {
        $user = $this->connect()->prepare("UPDATE diario_de_classe SET presenca = :presenca WHERE id=:id");

        $user->execute([
            'presenca' => $presenca,
            'id' => $diario,
        ]);        
    }
    
    public function verComentariosDoAlunoDaTurma($aluno, $disciplina, $turma, $data, $professor)
    {
        $comentarioQuery = $this->connect()->query("
            select *
            from diario_de_classe
            where aluno = $aluno
            and disciplina = $disciplina
            and turma = $turma
            and data = '$data'
            and professor = $professor
            and contexto = 'observacao'
        ");
        return $comentarioQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function adicionarComentario($turma, $aluno, $disciplina, $mensagem, $data, $prof)
    {
        $user = $this->connection->prepare("
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
        
        return $this->connection->lastInsertId();
    }
}
