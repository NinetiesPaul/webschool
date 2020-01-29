<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class ResponsavelStorage extends DB
{
    public $localConnection;
    
    public function __construct() {
        parent::__construct();
        $this->localConnection = $this->connect();   
    }
    public function verResponsaveis()
    {
        $responsavelQuery = $this->connect()->query("select usuario.* from usuario, responsavel where usuario.id=responsavel.usuario");
        return $responsavelQuery->fetchAll(PDO::FETCH_OBJ);        
    }
    
    public function verResponsavel($idResponsavel)
    {
        $responsavelQuery = $this->connect()->query("select usuario.*,responsavel.id as responsavel from usuario, responsavel where usuario.id=responsavel.usuario and responsavel.usuario = $idResponsavel");
        return $responsavelQuery->fetch(PDO::FETCH_OBJ);
    }
    
    public function adicionarResponsavel($data)
    {
        $email = $data['email'];
        $nome = $data['nome'];
        $password = $data['password'];
        $salt = time() + rand(100, 1000);
        $password = md5($password . $salt);
        
        if ($this->util()->loginTakenBackEnd($email, "responsavel")) {
            return false;
        }

        $idEndereco = $this->endereco()->inserirEndereco();

        $usario = [
            'name' => $nome,
            'email' => $email,
            'password' => $password,
            'salt' => $salt,
            'endereco' => $idEndereco,
        ];

        $userId = $this->usuario()->inserirUsuario($usuario);

        $responsavel = $this->connect()->prepare("INSERT INTO responsavel (usuario) VALUES (:idUusuario)");
        $responsavel->execute([
            'idUusuario' => $userId,
        ]);

        $this->avatar()->inserirAvatar($userId);
    }
    
    public function alterarResponsavel($data)
    {
        $userId = $data['id'];
        $nome = $data['nome'];
        $email = $data['email'];
        $password = $data['password'];
        $salt = $data['salt'];
        $newPassword = md5($password);
        
        if ($this->util()->loginTakenBackEnd($email, "responsavel", $userId)) {
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
            $password = $data['password'];
            $password = md5($password . $salt);

            $sql .= ' ,pass=:pass';
            $fields['pass'] = $password;
        }

        $sql .= ' where id=:userId';
        $fields['userId'] = $userId;

        $user = $this->connect()->prepare($sql);
        $user->execute($fields);
    }
    
    public function removerResponsavel($idResponsavel)
    {
        $user = $this->connect()->prepare("UPDATE usuario SET is_deleted = 1 WHERE id = :id");

        $user->execute([
            'id' => $idResponsavel,
        ]);
    }
    
    public function adicionarAlunoPorResponsavel($data)
    {
        $responsavel = $data['responsavel'];
        $aluno = $data['aluno'];

        $user = $this->connect()->prepare("INSERT INTO responsavel_por_aluno (responsavel, aluno)
                VALUES (:responsavel, :aluno)");

        $count = $user->execute([
            'responsavel' => $responsavel,
            'aluno' => $aluno,
        ]);
        
        return $data['id'];
    }
    
    public function removerAlunoPorResponsavel($id)
    {
        $user = $this->connect()->prepare("DELETE from responsavel_por_aluno WHERE id = :id");

        $user->execute([
            'id' => $id,
        ]);
    }
    
    public function verAlunosDoResponsavel($responsavel)
    {
        $meusAlunosQuery = $this->connect()->query("select * from responsavel_por_aluno where responsavel = $responsavel");
        return $meusAlunosQuery->fetchAll(PDO::FETCH_OBJ);
    }
}
