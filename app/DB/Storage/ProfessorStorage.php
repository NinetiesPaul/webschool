<?php

namespace App\DB\Storage;

use App\DB\DB;
use App\Enum;
use PDO;

class ProfessorStorage extends DB
{
    public function verProfessores()
    {
        $professorQuery = $this->db->query("
            SELECT usuario.*,professor.id AS professor
            FROM usuario, professor
            WHERE usuario.id = professor.usuario
        ");

        return $professorQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verProfessor($idProfessor)
    {
        $professorQuery = $this->db->query("
            SELECT usuario.*,professor.id AS professor
            FROM usuario, professor
            WHERE usuario.id = professor.usuario AND professor.usuario = $idProfessor
        ");

        return $professorQuery->fetch(PDO::FETCH_OBJ);
    }

    public function adicionarProfessor($email, $nome, $password, $salt)
    {
        $usuarioStorage = new UsuarioStorage($this->db);
        if ($usuarioStorage->loginTaken($email, Enum::TIPO_PROFESSOR)) {
            return false;
        }
        
        $enderecoStorage = new EnderecoStorage($this->db);
        $idEndereco = $enderecoStorage->inserirEndereco();

        $userId = $usuarioStorage->inserirUsuario([
            'name' => $nome,
            'email' => $email,
            'password' => $password,
            'salt' => $salt,
            'endereco' => $idEndereco,
        ]);

        $professor = $this->db->prepare("INSERT INTO professor (usuario) VALUES (:idUusuario)");
        $professor->execute([
            'idUusuario' => $userId,
        ]);

        $avatarStorage = new AvatarStorage($this->db);
        $avatarStorage->inserirUsuarioNaAvatar($userId);
    }
    
    // todo: refatorar metodo e quebrar cada chamada de deleção para o storage pertinente
    public function removerProfessor($professor, $usuario, $endereco, $footprint)
    {
        $enderecoStorage = new EnderecoStorage($this->db);
        $enderecoStorage->deletarEndereco($usuario, $endereco);

        $avatarStorage = new AvatarStorage($this->db);
        $avatarStorage->deletarAvatarDoUsuario($usuario);

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

        $usuarioStorage = new UsuarioStorage($this->db);
        $usuarioStorage->deletarUsuario($usuario);
        
        $footprintBackup = $this->db->prepare("INSERT INTO usuarios_deletados (tipo, nome, footprint, deletado_em) VALUES ('professor', :nome, :footprint, NOW())");
        $footprintBackup->execute([
            'nome' => $footprint['usuario']->nome,
            'footprint' => json_encode($footprint),
        ]);
    }
    
    public function desativarProfessor($idProfessor)
    {
        $alunoQuery = $this->db->query("
            SELECT usuario.is_deleted
            FROM usuario, professor
            WHERE usuario.id=professor.usuario AND professor.usuario = $idProfessor
        ");

        if ($alunoQuery->rowCount() === 0) {
            $this->throwError("<b>Erro</b>: id inválido");
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
        $disciplinaPorProfessor = $this->db->prepare("INSERT INTO disciplina_por_professor (professor, disciplina, turma) VALUES (:idProfessor, :idDisciplina, :idTurma)");

        $disciplinaPorProfessor->execute([
            'idProfessor' => $professor,
            'idDisciplina' => $disciplina,
            'idTurma' => $turma,
        ]);
        
        $turmaStorage = new TurmaStorage($this->db);
        $alunos = $turmaStorage->verAlunosDaTurma($turma);

        foreach ($alunos as $aluno) {           
            $notaStorage = new NotaStorage($this->db);
            $notaStorage->inserirNota([
                'idAluno' => $aluno->id,
                'idDisciplina' => $disciplina,
                'idTurma' => $turma,
            ]);
                        
            $diarioStorage = new DiarioDeClasseStorage($this->db);
            $diarioStorage->inserirDiarioDeClasse([
                'idAluno' => $aluno->id,
                'idDisciplina' => $disciplina,
                'idTurma' => $turma,
            ]);
        }
    }

    public function verificarMateriaPorProfessor($disciplina, $turma, $professor)
    {
        $professorQuery = $this->db->query("
            SELECT *
            FROM disciplina_por_professor
            WHERE disciplina = $disciplina AND turma = $turma AND professor = $professor
        ");

        return $professorQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function removerProfessorPorMateria($id)
    {
        $user = $this->db->prepare("DELETE FROM disciplina_por_professor WHERE id = :id");
        $user->execute([
            'id' => $id,
        ]);

        if ($user->rowCount() === 0) {
            $this->throwError("Erro ao remover disciplina por professor: id inválido");
        }
    }
    
    public function verProfessorPorMateria($id)
    {
        $professorQuery = $this->db->query("
            SELECT *
            FROM disciplina_por_professor
            WHERE professor = $id
        ");

        return $professorQuery->fetchAll(PDO::FETCH_OBJ);
    }

    private function throwError($msg)
    {
        throw new \Exception($msg);
    }
}
