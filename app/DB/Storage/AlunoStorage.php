<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class AlunoStorage extends DB
{
    public $localConnection;
    
    public function __construct() {
        parent::__construct();
        $this->localConnection = $this->connect();   
    }
    
    public function verAlunos()
    {
        $alunoQuery = $this->connect()->query("select usuario.*,aluno.id as aluno from usuario, aluno where usuario.id=aluno.usuario");
        return $alunoQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verAluno(int $aluno)
    {
        $alunoQuery = $this->connect()->query("select usuario.*,aluno.id as aluno from usuario, aluno where usuario.id=aluno.usuario and aluno.usuario = $aluno");
        return $alunoQuery->fetch(PDO::FETCH_OBJ);
    }

    public function adicionarAluno($email, $nome, $password, $salt, $turma)
    {   
        if ($this->util()->loginTakenBackEnd($email, "aluno")) {
            return false;
        }
        
        $idEndereco = $this->endereco()->inserirEndereco();
        
        $usuario = [
            'name' => $nome,
            'email' => $email,
            'password' => $password,
            'endereco' => $idEndereco,
            'salt' => $salt,
        ];

        $userId = $this->usuario()->inserirUsuario($usuario);

        $this->avatar()->inserirAvatar($userId);

        $aluno = $this->localConnection->prepare("INSERT INTO aluno (usuario, turma) VALUES (:idUusuario, :idTurma)");
        $aluno->execute([
            'idUusuario' => $userId,
            'idTurma' => $turma,
        ]);

        $lastid = (int) $this->localConnection->lastInsertId();        
        
        $disciplinas = $this->materia()->verMateriaPorProfessorPorTurma($turma);

        foreach ($disciplinas as $disciplina) {
            
            $nota = [
                'idAluno' => $lastid,
                'idDisciplina' => $disciplina->disciplina,
                'idTurma' => $turma,
            ];
            
            $this->nota()->inserirNota($nota);
            
            $diario = [
                'idAluno' => $lastid,
                'idDisciplina' => $disciplina->disciplina,
                'idTurma' => $turma,
            ];
            
            $this->diario()->inserirDiarioDeClasse($diario);
        }
    }

    public function alterarAluno($userId, $idAluno, $nome, $email, $password, $salt, $turma)
    {   
        if ($this->util()->loginTakenBackEnd($email, "aluno", $userId)) {
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

        $alunoQuery = $this->connect()->prepare($sql);
        $alunoQuery->execute($fields);

        $turmaQuery = $this->connect()->prepare("UPDATE aluno SET turma=:idTurma WHERE usuario=:idUusuario");
        $turmaQuery->execute([
            'idTurma' => $turma,
            'idUusuario' => $userId,
        ]);
        
        $disciplinas = $this->materia()->verMateriaPorProfessorPorTurma($turma);

        foreach ($disciplinas as $disciplina) {
            $checkDisciplinaQuery = $this->connect()->query("SELECT id FROM nota_por_aluno WHERE turma = $turma and aluno = $idAluno and disciplina = $disciplina->disciplina");
            $checkDisciplina = $checkDisciplinaQuery->fetchAll(PDO::FETCH_OBJ);
            
            if (empty($checkDisciplina)) {
                $nota = [
                    'idAluno' => $idAluno,
                    'idDisciplina' => $disciplina->disciplina,
                    'idTurma' => $turma,
                ];
                
                $this->nota()->inserirNota($nota);
            }
            
            $checkDiarioQuery = $this->connect()->query("SELECT id FROM diario_de_classe WHERE turma = $turma and aluno = $idAluno and disciplina = $disciplina->disciplina");
            $checkDiario = $checkDiarioQuery->fetchAll(PDO::FETCH_OBJ);

            if (empty($checkDiario)) {
                $diario = [
                    'idAluno' => $idAluno,
                    'idDisciplina' => $disciplina->disciplina,
                    'idTurma' => $turma,
                ];

                $this->diario()->inserirDiarioDeClasse($diario);
            }
        }
    }

    public function removerAluno($aluno, $usuario, $endereco, $footprint)
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

        $user = $this->connect()->prepare("DELETE FROM arquivos WHERE diario in (SELECT id FROM diario_de_classe WHERE aluno = :aluno AND contexto = 'observacao');");

        $user->execute([
            'aluno' => $aluno,
        ]);

        $user = $this->connect()->prepare("DELETE FROM responsavel_por_aluno WHERE aluno = :aluno;");

        $user->execute([
            'aluno' => $aluno,
        ]);

        $user = $this->connect()->prepare("DELETE FROM diario_de_classe WHERE aluno = :aluno;");

        $user->execute([
            'aluno' => $aluno,
        ]);

        $user = $this->connect()->prepare("DELETE FROM nota_por_aluno WHERE aluno = :aluno;");

        $user->execute([
            'aluno' => $aluno,
        ]);

        $user = $this->connect()->prepare("DELETE FROM aluno WHERE usuario = :id;");

        $user->execute([
            'id' => $usuario,
        ]);

        $user = $this->connect()->prepare("DELETE FROM usuario WHERE id = :id;");

        $user->execute([
            'id' => $usuario,
        ]);
        
        $footprintBackup = $this->localConnection->prepare("INSERT INTO usuarios_deletados (tipo, nome, footprint, deletado_em) VALUES ('aluno', :nome, :footprint, NOW())");
        $footprintBackup->execute([
            'nome' => $footprint['usuario']->nome,
            'footprint' => json_encode($footprint),
        ]);
    }

    public function desativarAluno($aluno)
    {
        $alunoQuery = $this->connect()->query("select usuario.is_deleted from usuario, aluno where usuario.id=aluno.usuario and aluno.usuario = $aluno");
        $is_deleted = $alunoQuery->fetch(PDO::FETCH_OBJ)->is_deleted;
        
        $is_deleted = ($is_deleted == 1) ? 0 : 1;
        
        $user = $this->connect()->prepare("UPDATE usuario SET is_deleted = :is_deleted WHERE id = :id");

        $user->execute([
            'is_deleted' => $is_deleted,
            'id' => $aluno,
        ]);
    }
    
    public function verAlunosDoResponsavel($responsavel)
    {
        $usersQuery = $this->connect()->query("
            select distinct usuario.*, responsavel_por_aluno.id as rpa from usuario, aluno, responsavel_por_aluno
            where aluno.usuario = usuario.id
            and aluno.id = responsavel_por_aluno.aluno
            and responsavel_por_aluno.responsavel = $responsavel
        ");
        return $usersQuery->fetchAll(PDO::FETCH_OBJ);        
    }

    public function verTurmaDoAluno($usuario)
    {
        $usuarioQuery = $this->connect()->query("select turma from aluno where usuario = $usuario");
        return $usuarioQuery->fetch(PDO::FETCH_OBJ);
    }
}
