<?php

namespace App\DB\Storage;

use App\DB\DB;
use App\Enum;
use PDO;

class UsuarioStorage
{
    protected $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function inserirUsuario($usuario)
    {
        $user = $this->db->prepare("
            INSERT INTO usuario (nome, email, pass, endereco, salt)
            VALUES (:name, :email, :password, :endereco, :salt)
        ");

        $user->execute($usuario);
        
        return (int) $this->db->lastInsertId();
    }

    public function alterarUsuario($userId, $nome, $email, $password, $salt, $tipo, $telefone1 = false, $telefone2 = false)
    {
        if ($this->loginTaken($email, $tipo, $userId)) {
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

        if ($telefone1) {
            $sql .= ", telefone1=:tel1";
            $fields['tel1'] = $telefone1;
        }

        if ($telefone2) {
            $sql .= ", telefone2=:tel2";
            $fields['tel2'] = $telefone2;
        }

        if (strlen($password) > 0) {
            $password = md5($password . $salt);

            $sql .= ' ,pass=:pass';
            $fields['pass'] = $password;
        }

        $sql .= ' where id=:userId';
        $fields['userId'] = $userId;

        $user = $this->db->prepare($sql);
        $user->execute($fields);

        return true;
    }

    public function loginTaken($login, $tipo, $id = false)
    {
        $query = "
            SELECT usuario.id FROM usuario,$tipo
            WHERE usuario.id = $tipo.usuario
            and usuario.email = '$login'
        ";

        if ($id) {
            $query .= " and usuario.id != $id";
        }

        $userQuery = $this->db->query($query);
        $userQuery = $userQuery->fetchObject();

        $res = false;
        if ($userQuery) {
            $res = true;
        }

        return $res;
    }

    public function verificarUsuario($alias, $turma, $tipo, $email)
    {
        $query = "
            SELECT u.*, $alias.id AS $tipo $turma
                FROM usuario u
                JOIN $tipo $alias ON $alias.usuario = u.id
                WHERE u.id = $alias.usuario
                AND u.email = '$email'
        ";

        $usersQuery = $this->db->query($query);

        return $usersQuery->fetchObject();
    }
}
