<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;

class ResponsavelStorage extends DB
{
    public $localConnection;
    
    public function __construct()
    {
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
    
    public function adicionarResponsavel($email, $nome, $password, $salt)
    {
        if ($this->util()->loginTakenBackEnd($email, "responsavel")) {
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

        $responsavel = $this->connect()->prepare("INSERT INTO responsavel (usuario) VALUES (:idUusuario)");
        $responsavel->execute([
            'idUusuario' => $userId,
        ]);

        $this->avatar()->inserirAvatar($userId);
    }
    
    public function alterarResponsavel($userId, $nome, $email, $password, $salt)
    {
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
            $password = md5($password . $salt);

            $sql .= ' ,pass=:pass';
            $fields['pass'] = $password;
        }

        $sql .= ' where id=:userId';
        $fields['userId'] = $userId;

        $user = $this->connect()->prepare($sql);
        $user->execute($fields);
    }
    
    public function removerResponsavel($responsavel, $usuario, $endereco, $footprint)
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

        $user = $this->connect()->prepare("DELETE FROM responsavel_por_aluno WHERE responsavel = :aluno;");

        $user->execute([
            'aluno' => $responsavel,
        ]);

        $user = $this->connect()->prepare("DELETE FROM responsavel WHERE id = :responsavel;");

        $user->execute([
            'responsavel' => $responsavel,
        ]);

        $user = $this->connect()->prepare("DELETE FROM usuario WHERE id = :id;");

        $user->execute([
            'id' => $usuario,
        ]);
        
        $footprintBackup = $this->localConnection->prepare("INSERT INTO usuarios_deletados (tipo, nome, footprint, deletado_em) VALUES ('responsavel', :nome, :footprint, NOW())");
        $footprintBackup->execute([
            'nome' => $footprint['usuario']->nome,
            'footprint' => json_encode($footprint),
        ]);
    }
    
    public function adicionarAlunoPorResponsavel($responsavel, $aluno, $id)
    {
        $user = $this->connect()->prepare("INSERT INTO responsavel_por_aluno (responsavel, aluno)
                VALUES (:responsavel, :aluno)");

        $count = $user->execute([
            'responsavel' => $responsavel,
            'aluno' => $aluno,
        ]);
        
        return $id;
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
    
    public function verAlunosDoResponsavelPorAluno($aluno)
    {
        $meusAlunosQuery = $this->connect()->query("select * from responsavel_por_aluno where aluno = $aluno");
        return $meusAlunosQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function desativarResponsavel($responsavel)
    {
        $responsavelQuery = $this->connect()->query("select usuario.is_deleted from usuario, responsavel where usuario.id=responsavel.usuario and responsavel.usuario = $responsavel");
        $is_deleted = $responsavelQuery->fetch(PDO::FETCH_OBJ)->is_deleted;
        
        $is_deleted = ($is_deleted == 1) ? 0 : 1;
        
        $user = $this->connect()->prepare("UPDATE usuario SET is_deleted = :is_deleted WHERE id = :id");

        $user->execute([
            'is_deleted' => $is_deleted,
            'id' => $responsavel,
        ]);
    }
}
