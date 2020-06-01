<?php

namespace App\DB\Storage;

use App\DB\DB;
use App\Enum;
use PDO;

class ProfessorStorage
{
    public $db;
    
    public function __construct()
    {
        $this->db = new DB();
    }
    
    public function verProfessores()
    {
        $professorQuery = $this->db->query("select usuario.*,professor.id as professor from usuario, professor where usuario.id = professor.usuario");
        return $professorQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verProfessor($idProfessor)
    {
        $professorQuery = $this->db->query("select usuario.*,professor.id as professor from usuario, professor where usuario.id = professor.usuario and professor.usuario = $idProfessor");
        return $professorQuery->fetch(PDO::FETCH_OBJ);
    }

    public function adicionarProfessor($email, $nome, $password, $salt)
    {
        if ($this->db->usuario()->loginTakenBackEnd($email, Enum::TIPO_PROFESSOR)) {
            return false;
        }
        
        $idEndereco = $this->db->endereco()->inserirEndereco();

        $usuario = [
            'name' => $nome,
            'email' => $email,
            'password' => $password,
            'salt' => $salt,
            'endereco' => $idEndereco,
        ];

        $userId = $this->db->usuario()->inserirUsuario($usuario);

        $this->db->avatar()->inserirAvatar($userId);

        $professor = $this->db->prepare("INSERT INTO professor (usuario) VALUES (:idUusuario)");
        $professor->execute([
            'idUusuario' => $userId,
        ]);
    }
    
    public function alterarProfessor($userId, $nome, $email, $password, $salt)
    {
        if ($this->db->usuario()->loginTakenBackEnd($email, Enum::TIPO_PROFESSOR, $userId)) {
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

        $user = $this->db->prepare($sql);
        $user->execute($fields);
    }
    
    public function removerProfessor($professor, $usuario, $endereco, $footprint)
    {
        $user = $this->db->prepare("UPDATE usuario SET endereco = NULL WHERE id = :id;");

        $user->execute([
            'id' => $usuario,
        ]);

        $user = $this->db->prepare("DELETE FROM endereco WHERE id = :endereco;");

        $user->execute([
            'endereco' => $endereco,
        ]);

        $user = $this->db->prepare("DELETE FROM fotos_de_avatar WHERE usuario = :id;");

        $user->execute([
            'id' => $usuario,
        ]);

        $user = $this->db->prepare("DELETE FROM arquivos WHERE diario in (SELECT id FROM diario_de_classe WHERE professor = :professor AND contexto = 'observacao');");

        $user->execute([
            'professor' => $professor,
        ]);

        $user = $this->db->prepare("DELETE FROM diario_de_classe WHERE professor = :professor;");

        $user->execute([
            'professor' => $professor,
        ]);

        $user = $this->db->prepare("DELETE FROM disciplina_por_professor WHERE professor = :professor;");

        $user->execute([
            'professor' => $professor,
        ]);

        $user = $this->db->prepare("DELETE FROM professor WHERE usuario = :id;");

        $user->execute([
            'id' => $usuario,
        ]);

        $user = $this->db->prepare("DELETE FROM usuario WHERE id = :id;");

        $user->execute([
            'id' => $usuario,
        ]);
        
        $footprintBackup = $this->db->prepare("INSERT INTO usuarios_deletados (tipo, nome, footprint, deletado_em) VALUES ('professor', :nome, :footprint, NOW())");
        $footprintBackup->execute([
            'nome' => $footprint['usuario']->nome,
            'footprint' => json_encode($footprint),
        ]);
    }
    
    public function desativarProfessor($idProfessor)
    {
        $alunoQuery = $this->db->query("select usuario.is_deleted from usuario, professor where usuario.id=professor.usuario and professor.usuario = $idProfessor");

        if ($alunoQuery->rowCount() === 0) {
            $this->throwError("Erro ao recuperar professor: id inválido");
        }

        $is_deleted = $alunoQuery->fetch(PDO::FETCH_OBJ)->is_deleted;
        
        $is_deleted = ($is_deleted == 1) ? 0 : 1;
        
        $user = $this->db->prepare("UPDATE usuario SET is_deleted = :is_deleted WHERE id = :id");

        $user->execute([
            'is_deleted' => $is_deleted,
            'id' => $idProfessor,
        ]);

        if ($user->rowCount() === 0) {
            $this->throwError("Erro ao atualizar professor: id inválido");
        }
    }
    
    public function adicionarMateriaPorProfessor($disciplina, $turma, $professor)
    {
        $user = $this->db->prepare("INSERT INTO disciplina_por_professor (professor, disciplina, turma)
                VALUES (:idProfessor, :idDisciplina, :idTurma)");

        $user->execute([
            'idProfessor' => $professor,
            'idDisciplina' => $disciplina,
            'idTurma' => $turma,
        ]);
        
        $alunos = $this->db->turma()->verAlunosDaTurma($turma);

        foreach ($alunos as $aluno) {
            $nota = [
                'idAluno' => $aluno->id,
                'idDisciplina' => $disciplina,
                'idTurma' => $turma,
            ];
            
            $this->db->nota()->inserirNota($nota);
            
            $diario = [
                'idAluno' => $aluno->id,
                'idDisciplina' => $disciplina,
                'idTurma' => $turma,
            ];
            
            $this->db->diario()->inserirDiarioDeClasse($diario);
        }
    }

    public function verificarMateriaPorProfessor($disciplina, $turma, $professor)
    {
        $professorQuery = $this->db->query("select * from disciplina_por_professor where disciplina = $disciplina and turma = $turma and professor = $professor");
        return $professorQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function removerProfessorPorMateria($id)
    {
        $user = $this->db->prepare("DELETE from disciplina_por_professor WHERE id = :id");

        $user->execute([
            'id' => $id,
        ]);

        if ($user->rowCount() === 0) {
            $this->throwError("Erro ao remover disciplina por professor: id inválido");
        }
    }
    
    public function verProfessorPorMateria($id)
    {
        $professorQuery = $this->db->query("SELECT * from disciplina_por_professor WHERE professor = $id");
        return $professorQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verMeusAlunos($turma, $disciplina, $professor)
    {
        $alunosQuery = $this->db->query("
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

    private function throwError($msg)
    {
        throw new \Exception($msg);
    }
}
