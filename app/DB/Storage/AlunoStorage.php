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
        $alunoQuery = $this->connect()->query("select usuario.* from usuario, aluno where usuario.id=aluno.usuario");
        return $alunoQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verAluno(int $aluno)
    {
        $alunoQuery = $this->connect()->query("select usuario.*,aluno.id as aluno from usuario, aluno where usuario.id=aluno.usuario and aluno.usuario = $aluno");
        return $alunoQuery->fetch(PDO::FETCH_OBJ);
    }

    public function adicionarAluno($data)
    {
        $email = $data['email'];
        $nome = $data['nome'];
        $password = $data['password'];
        $salt = time() + rand(100, 1000);
        $password = md5($password . $salt);
        $turma = $data['turma'];
        
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

    public function alterarAluno()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $userId = $data['id'];
        $idAluno = $data['idAluno'];
        $nome = $data['nome'];
        $email = $data['email'];
        $password = $data['password'];
        $salt = $data['salt'];
        $turma = $data['turma'];
        $turmaAtual = $data['turmaAtual'];
        
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
            $password = $_POST['password'];
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

    public function removerAluno($aluno)
    {
        $user = $this->connect()->prepare("UPDATE usuario SET is_deleted = 1 WHERE id = :id");

        $user->execute([
            'id' => $aluno,
        ]);
    }
}
