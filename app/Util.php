<?php

namespace App;

use App\DB\DB;

class Util
{
    protected $connection;
    protected $template;

    public function __construct()
    {
        $this->template = new Templates();
        $this->connection = new DB();
    }

    public function userPermission($tipo)
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

    public function loadTemplate($path, $args = null)
    {
        $template 	= $this->template->getTemplate($path);
        if ($args) {
            $template = $this->template->parseTemplate($template, $args);
        }
        echo $template;
    }
}
