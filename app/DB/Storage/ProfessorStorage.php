<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class ProfessorStorage extends DB
{
    public $localConnection;
    
    public function __construct() {
        parent::__construct();
        $this->localConnection = $this->connect();   
    }
    
    public function verProfessores()
    {
        $professorQuery = $this->connect()->query("select usuario.*,professor.id as professor from usuario, professor where usuario.id = professor.usuario");
        return $professorQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verProfessor($idProfessor)
    {
        $professorQuery = $this->connect()->query("select usuario.*,professor.id as professor from usuario, professor where usuario.id = professor.usuario and professor.usuario = $idProfessor");
        return $professorQuery->fetch(PDO::FETCH_OBJ);
    }

    public function adicionarProfessor($email, $nome, $password, $salt)
    {
        if ($this->util()->loginTakenBackEnd($email, "professor")) {
            return false;
        }
        
        $idEndereco = $this->endereco()->inserirEndereco();

        $usuario = [
            'name' => $nome,
            'email' => $email,
            'password' => $password,
            'salt' => $salt,
            'endereco' => $idEndereco,
        ];

        $userId = $this->usuario()->inserirUsuario($usuario);

        $this->avatar()->inserirAvatar($userId);

        $professor = $this->connect()->prepare("INSERT INTO professor (usuario) VALUES (:idUusuario)");
        $professor->execute([
            'idUusuario' => $userId,
        ]);
    }
    
    public function alterarProfessor($userId, $nome, $email, $password, $salt)
    {   
        if ($this->util()->loginTakenBackEnd($email, "professor", $userId)) {
            return false;
        }

        $sql = "
            UPDATE usuario
            SET nome=:nome, email=:email
            ";

        $fields = [
            'nome' => $nome,
            'email' => $email,
        ];

        if (strlen($password) > 0) {
            $password = md5($password . $salt);

            $sql .= ' ,pass=:pass';
            $fields['pass'] = $password;
        }

        $sql .= ' where id=:userId';
        $fields['userId'] = $userId;

        $user = $this->connect()->prepare($sql);
        $user->execute($fields);
    }
    
    public function removerProfessor($idProfessor)
    {
        $user = $this->connect()->prepare("UPDATE usuario SET is_deleted = 1 WHERE id = :id");

        $user->execute([
            'id' => $idProfessor,
        ]);
    }
    
    public function adicionarMateriaPorProfessor($disciplina, $turma, $professor)
    {
        $user = $this->connect()->prepare("INSERT INTO disciplina_por_professor (professor, disciplina, turma)
                VALUES (:idProfessor, :idDisciplina, :idTurma)");

        $user->execute([
            'idProfessor' => $professor,
            'idDisciplina' => $disciplina,
            'idTurma' => $turma,
        ]);
        
        $alunos = $this->turma()->verAlunosDaTurma($turma);

        foreach ($alunos as $aluno) {           
            $nota = [
                'idAluno' => $aluno->id,
                'idDisciplina' => $disciplina,
                'idTurma' => $turma,
            ];
            
            $this->nota()->inserirNota($nota);
            
            $diario = [
                'idAluno' => $aluno->id,
                'idDisciplina' => $disciplina,
                'idTurma' => $turma,
            ];
            
            $this->diario()->inserirDiarioDeClasse($diario);
        }
    }
    
    public function removerProfessorPorMateria($id)
    {
        $user = $this->connect()->prepare("DELETE from disciplina_por_professor WHERE id = :id");

        $user->execute([
            'id' => $id,
        ]);
    }

    public function verMeusAlunos($turma, $disciplina, $professor)
    {
        $alunosQuery = $this->connect()->query("
                select distinct usuario.* from usuario, aluno, turma, disciplina_por_professor
                where usuario.id=aluno.usuario
                and aluno.turma=disciplina_por_professor.turma
                and disciplina_por_professor.turma=$turma
                and disciplina_por_professor.disciplina=$disciplina
                and disciplina_por_professor.professor=$professor
                order by usuario.nome
            ");
        return $alunosQuery->fetchAll(PDO::FETCH_OBJ);
    }
}
