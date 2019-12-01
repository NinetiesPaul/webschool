<?php

namespace App;

use App\DB\DB;

class Util {
    
    protected $connection;
    
    public function __construct() {
        $this->connection = new DB();
    }

    public function userPermission(string $tipo)
    {
        session_start();
        $session_type = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
        
        if (!$session_type || $session_type !== $tipo) {
            header('Location: /webschool/');
        }
    }
    
    public function loginTakenAjax() 
    {
        $data = json_decode(json_encode($_POST), true);
        
        $login = $data["login"];
        $tipo = $data["tipo"];
        $id = (isset($data["id"])) ? $data['id'] : null;
        
        $query = "
            SELECT usuario.id FROM usuario,$tipo
            WHERE usuario.id = $tipo.usuario
            and usuario.email = '$login'
        ";

        if ($id) {
            $query .= " and usuario.id != $id";
        }

        $cidadeQuery = $this->connection->query($query);
        $cidadeQuery = $cidadeQuery->fetchObject();

        $res = false;
        if ($cidadeQuery) {
            $res = true;
        }

        echo $res;
    }

    public function loginTakenBackEnd(string $login, string $tipo, $id = false)
    {       
        $query = "
            SELECT usuario.id FROM usuario,$tipo
            WHERE usuario.id = $tipo.usuario
            and usuario.email = '$login'
        ";

        if ($id) {
            $query .= " and usuario.id != $id";
        }

        $userQuery = $this->connection->query($query);
        $userQuery = $userQuery->fetchObject();

        $res = false;
        if ($userQuery) {
            $res = true;
        }

        return $res;
    }
    
    public function pegarTurmaDoAlunoPorUsuario(int $idUsuario)
    {
        $alunoQuery = $this->connection->query("
            select * from aluno where usuario = $idUsuario
        ");
        $alunoQuery = $alunoQuery->fetchObject();

        $turmaQuery = $this->connection->query("
            select * from turma where id = $alunoQuery->turma
        ");
        $turmaQuery = $turmaQuery->fetchObject();

        $nomeTurma = 'Sem turma';
        if ($turmaQuery) {
            $nomeTurma = " na " . $turmaQuery->serie.'º Série '.$turmaQuery->nome;
        }

        return $nomeTurma;        
    }
}
