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
    
    public function removerProfessor($professor, $usuario, $endereco, $footprint)
    {
        $user = $this->connect()->prepare("UPDATE usuario SET endereco = NULL WHERE id = :id;");

        $user->execute([
            'id' => $usuario,
        ]);

        $user = $this->connect()->prepare("DELETE FROM endereco WHERE id = :endereco;");

        $user->execute([
            'endereco' => $endereco,
        ]);

        $user = $this->connect()->prepare("DELETE FROM fotos_de_avatar WHERE usuario = :id;");

        $user->execute([
            'id' => $usuario,
        ]);

        $user = $this->connect()->prepare("DELETE FROM arquivos WHERE diario in (SELECT id FROM diario_de_classe WHERE professor = :professor AND contexto = 'observacao');");

        $user->execute([
            'professor' => $professor,
        ]);

        $user = $this->connect()->prepare("DELETE FROM diario_de_classe WHERE professor = :professor;");

        $user->execute([
            'professor' => $professor,
        ]);

        $user = $this->connect()->prepare("DELETE FROM disciplina_por_professor WHERE professor = :professor;");

        $user->execute([
            'professor' => $professor,
        ]);

        $user = $this->connect()->prepare("DELETE FROM professor WHERE usuario = :id;");

        $user->execute([
            'id' => $usuario,
        ]);

        $user = $this->connect()->prepare("DELETE FROM usuario WHERE id = :id;");

        $user->execute([
            'id' => $usuario,
        ]);
        
        $footprintBackup = $this->localConnection->prepare("INSERT INTO usuarios_deletados (tipo, nome, footprint, deletado_em) VALUES ('professor', :nome, :footprint, NOW())");
        $footprintBackup->execute([
            'nome' => $footprint['usuario']->nome,
            'footprint' => json_encode($footprint),
        ]);
    }
    
    public function desativarProfessor($idProfessor)
    {
        $alunoQuery = $this->connect()->query("select usuario.is_deleted from usuario, professor where usuario.id=professor.usuario and professor.usuario = $idProfessor");
        $is_deleted = $alunoQuery->fetch(PDO::FETCH_OBJ)->is_deleted;
        
        $is_deleted = ($is_deleted == 1) ? 0 : 1;
        
        $user = $this->connect()->prepare("UPDATE usuario SET is_deleted = :is_deleted WHERE id = :id");

        $user->execute([
            'is_deleted' => $is_deleted,
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
    
    public function verProfessorPorMateria($id)
    {        
        $professorQuery = $this->connect()->query("SELECT * from disciplina_por_professor WHERE professor = $id");
        return $professorQuery->fetchAll(PDO::FETCH_OBJ);
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
