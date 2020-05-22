<?php

namespace App\DB\Storage;

use App\DB\DB;
use App\Enum;
use PDO;

class AlunoStorage
{
    public $db;
    
    public function __construct()
    {
        $this->db = new DB();
    }
    
    public function verAlunos()
    {
        $alunoQuery = $this->db->query("select usuario.*,aluno.id as aluno from usuario, aluno where usuario.id=aluno.usuario");
        return $alunoQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verAluno($aluno)
    {
        $alunoQuery = $this->db->query("select usuario.*,aluno.id as aluno from usuario, aluno where usuario.id=aluno.usuario and aluno.usuario = $aluno");
        return $alunoQuery->fetch(PDO::FETCH_OBJ);
    }

    public function adicionarAluno($email, $nome, $password, $salt, $turma)
    {
        if ($this->db->usuario()->loginTakenBackEnd($email, Enum::TIPO_ALUNO)) {
            return false;
        }
        
        $idEndereco = $this->db->endereco()->inserirEndereco();
        
        $usuario = [
            'name' => $nome,
            'email' => $email,
            'password' => $password,
            'endereco' => $idEndereco,
            'salt' => $salt,
        ];

        $userId = $this->db->usuario()->inserirUsuario($usuario);

        $this->db->avatar()->inserirAvatar($userId);

        $aluno = $this->db->prepare("INSERT INTO aluno (usuario, turma) VALUES (:idUusuario, :idTurma)");
        $aluno->execute([
            'idUusuario' => $userId,
            'idTurma' => $turma,
        ]);

        $lastid = (int) $this->db->lastInsertId();
        
        $disciplinas = $this->db->materia()->verMateriaPorProfessorPorTurma($turma);

        foreach ($disciplinas as $disciplina) {
            $nota = [
                'idAluno' => $lastid,
                'idDisciplina' => $disciplina->disciplina,
                'idTurma' => $turma,
            ];
            
            $this->db->nota()->inserirNota($nota);
            
            $diario = [
                'idAluno' => $lastid,
                'idDisciplina' => $disciplina->disciplina,
                'idTurma' => $turma,
            ];
            
            $this->db->diario()->inserirDiarioDeClasse($diario);
        }
    }

    public function alterarAluno($userId, $idAluno, $nome, $email, $password, $salt, $turma)
    {
        if ($this->db->usuario()->loginTakenBackEnd($email, Enum::TIPO_ALUNO, $userId)) {
            return false;
        }

        $sql = 'UPDATE usuario
            SET nome=:nome, email=:email';

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

        $alunoQuery = $this->db->prepare($sql);
        $alunoQuery->execute($fields);

        $turmaQuery = $this->db->prepare("UPDATE aluno SET turma=:idTurma WHERE usuario=:idUusuario");
        $turmaQuery->execute([
            'idTurma' => $turma,
            'idUusuario' => $userId,
        ]);
        
        $disciplinas = $this->db->materia()->verMateriaPorProfessorPorTurma($turma);

        foreach ($disciplinas as $disciplina) {
            $checkDisciplinaQuery = $this->db->query("SELECT id FROM nota_por_aluno WHERE turma = $turma and aluno = $idAluno and disciplina = $disciplina->disciplina");
            $checkDisciplina = $checkDisciplinaQuery->fetchAll(PDO::FETCH_OBJ);
            
            if (empty($checkDisciplina)) {
                $nota = [
                    'idAluno' => $idAluno,
                    'idDisciplina' => $disciplina->disciplina,
                    'idTurma' => $turma,
                ];
                
                $this->db->nota()->inserirNota($nota);
            }
            
            $checkDiarioQuery = $this->db->query("SELECT id FROM diario_de_classe WHERE turma = $turma and aluno = $idAluno and disciplina = $disciplina->disciplina");
            $checkDiario = $checkDiarioQuery->fetchAll(PDO::FETCH_OBJ);

            if (empty($checkDiario)) {
                $diario = [
                    'idAluno' => $idAluno,
                    'idDisciplina' => $disciplina->disciplina,
                    'idTurma' => $turma,
                ];

                $this->db->diario()->inserirDiarioDeClasse($diario);
            }
        }
    }

    public function removerAluno($aluno, $usuario, $endereco, $footprint)
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

        $user = $this->db->prepare("DELETE FROM arquivos WHERE diario in (SELECT id FROM diario_de_classe WHERE aluno = :aluno AND contexto = 'observacao');");

        $user->execute([
            'aluno' => $aluno,
        ]);

        $user = $this->db->prepare("DELETE FROM responsavel_por_aluno WHERE aluno = :aluno;");

        $user->execute([
            'aluno' => $aluno,
        ]);

        $user = $this->db->prepare("DELETE FROM diario_de_classe WHERE aluno = :aluno;");

        $user->execute([
            'aluno' => $aluno,
        ]);

        $user = $this->db->prepare("DELETE FROM nota_por_aluno WHERE aluno = :aluno;");

        $user->execute([
            'aluno' => $aluno,
        ]);

        $user = $this->db->prepare("DELETE FROM aluno WHERE usuario = :id;");

        $user->execute([
            'id' => $usuario,
        ]);

        $user = $this->db->prepare("DELETE FROM usuario WHERE id = :id;");

        $user->execute([
            'id' => $usuario,
        ]);
        
        $footprintBackup = $this->db->prepare("INSERT INTO usuarios_deletados (tipo, nome, footprint, deletado_em) VALUES ('aluno', :nome, :footprint, NOW())");
        $footprintBackup->execute([
            'nome' => $footprint['usuario']->nome,
            'footprint' => json_encode($footprint),
        ]);
    }

    public function desativarAluno($aluno)
    {
        $alunoQuery = $this->db->query("select usuario.is_deleted from usuario, aluno where usuario.id=aluno.usuario and aluno.usuario = $aluno");

        if ($alunoQuery->rowCount() === 0) {
            $this->throwError("Erro ao recuperar aluno (id inválido)");
        }

        $is_deleted = $alunoQuery->fetch(PDO::FETCH_OBJ)->is_deleted;
        
        $is_deleted = ($is_deleted == 1) ? 0 : 1;
        
        $user = $this->db->prepare("UPDATE usuario SET is_deleted = :is_deleted WHERE id = :id");

        $user->execute([
            'is_deleted' => $is_deleted,
            'id' => $aluno,
        ]);

        if ($user->rowCount() === 0) {
            $this->throwError("Erro ao atualizar aluno (id inválido)");
        }
    }
    
    public function verAlunosDoResponsavel($responsavel)
    {
        $usersQuery = $this->db->query("
            select distinct usuario.*, responsavel_por_aluno.id as rpa from usuario, aluno, responsavel_por_aluno
            where aluno.usuario = usuario.id
            and aluno.id = responsavel_por_aluno.aluno
            and responsavel_por_aluno.responsavel = $responsavel
        ");
        return $usersQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verTurmaDoAluno($usuario)
    {
        $usuarioQuery = $this->db->query("select turma from aluno where usuario = $usuario");
        return $usuarioQuery->fetch(PDO::FETCH_OBJ);
    }

    public function pegarNomeDoAlunoPorAlunoId($id)
    {
        $userQuery = $this->db->query("select usuario.* from usuario,aluno where usuario.id=aluno.usuario and aluno.id=$id");
        $user = $userQuery->fetchObject();

        return $user->nome;
    }

    public function pegarIdDaTurmaDoAlunoPorAlunoId($id)
    {
        $turmaQuery = $this->db->query("select turma from aluno where id=$id");
        $turma = $turmaQuery->fetchObject();

        return $turma->turma;
    }

    private function throwError($msg)
    {
        throw new \Exception($msg);
    }
}
