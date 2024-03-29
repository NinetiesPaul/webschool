<?php

namespace App\DB\Storage;

use App\DB\DB;
use App\Enum;
use PDO;

class ResponsavelStorage
{
    public $db;
    
    public function __construct()
    {
        $this->db = new DB();
    }

    public function verResponsaveis()
    {
        $responsavelQuery = $this->db->query("
            SELECT usuario.*
            FROM usuario, responsavel
            WHERE usuario.id=responsavel.usuario
        ");

        return $responsavelQuery->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function verResponsavel($idResponsavel)
    {
        $responsavelQuery = $this->db->query("
            SELECT usuario.*,responsavel.id AS responsavel
            FROM usuario, responsavel
            WHERE usuario.id=responsavel.usuario AND responsavel.usuario = $idResponsavel
        ");

        return $responsavelQuery->fetch(PDO::FETCH_OBJ);
    }
    
    public function adicionarResponsavel($email, $nome, $password, $salt)
    {
        $usuarioStorage = new UsuarioStorage();
        if ($usuarioStorage->loginTaken($email, Enum::TIPO_RESPONSAVEL)) {
            return false;
        }

        $enderecoStorage = new EnderecoStorage();
        $idEndereco = $enderecoStorage->inserirEndereco();

        $usuarioStorage = new UsuarioStorage();
        $userId = $usuarioStorage->inserirUsuario([
            'name' => $nome,
            'email' => $email,
            'password' => $password,
            'salt' => $salt,
            'endereco' => $idEndereco,
        ]);

        $responsavel = $this->db->prepare("INSERT INTO responsavel (usuario) VALUES (:idUusuario)");
        $responsavel->execute([
            'idUusuario' => $userId,
        ]);

        $avatarStorage = new AvatarStorage();
        $avatarStorage->inserirUsuarioNaAvatar($userId);
    }

    public function removerResponsavel($responsavel, $usuario, $endereco, $footprint)
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

        $user = $this->db->prepare("DELETE FROM responsavel_por_aluno WHERE responsavel = :aluno;");
        $user->execute([
            'aluno' => $responsavel,
        ]);

        $user = $this->db->prepare("DELETE FROM responsavel WHERE id = :responsavel;");
        $user->execute([
            'responsavel' => $responsavel,
        ]);

        $user = $this->db->prepare("DELETE FROM usuario WHERE id = :id;");
        $user->execute([
            'id' => $usuario,
        ]);
        
        $footprintBackup = $this->db->prepare("INSERT INTO usuarios_deletados (tipo, nome, footprint, deletado_em) VALUES ('responsavel', :nome, :footprint, NOW())");
        $footprintBackup->execute([
            'nome' => $footprint['usuario']->nome,
            'footprint' => json_encode($footprint),
        ]);
    }
    
    public function adicionarAlunoPorResponsavel($responsavel, $aluno)
    {
        $user = $this->db->prepare("INSERT INTO responsavel_por_aluno (responsavel, aluno) VALUES (:responsavel, :aluno)");
        $user->execute([
            'responsavel' => $responsavel,
            'aluno' => $aluno,
        ]);
    }
    
    public function removerAlunoPorResponsavel($id)
    {
        $user = $this->db->prepare("DELETE FROM responsavel_por_aluno WHERE id = :id");
        $user->execute([
            'id' => $id,
        ]);

        if ($user->rowCount() === 0) {
            $this->throwError("<b>Erro</b>: id inválido");
        }
    }
    
    public function verAlunosDoResponsavel($responsavel)
    {
        $meusAlunosQuery = $this->db->query("
            SELECT *
            FROM responsavel_por_aluno
            WHERE responsavel = $responsavel
        ");

        return $meusAlunosQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verResponsaveisPeloAluno($aluno)
    {
        $meusAlunosQuery = $this->db->query("
            SELECT *
            FROM responsavel_por_aluno
            WHERE aluno = $aluno
        ");

        return $meusAlunosQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function desativarResponsavel($responsavel)
    {
        $responsavelQuery = $this->db->query("
            SELECT usuario.is_deleted
            FROM usuario, responsavel
            WHERE usuario.id=responsavel.usuario AND responsavel.usuario = $responsavel
        ");

        if ($responsavelQuery->rowCount() === 0) {
            $this->throwError("<b>Erro</b>: id inválido");
        }

        $is_deleted = $responsavelQuery->fetch(PDO::FETCH_OBJ)->is_deleted;
        
        $is_deleted = ($is_deleted == 1) ? 0 : 1;
        
        $user = $this->db->prepare("UPDATE usuario SET is_deleted = :is_deleted WHERE id = :id");
        $user->execute([
            'is_deleted' => $is_deleted,
            'id' => $responsavel,
        ]);

        if ($user->rowCount() === 0) {
            $this->throwError("Erro ao atualizar responsavel: id inválido");
        }
    }

    private function throwError($msg)
    {
        throw new \Exception($msg);
    }
}
