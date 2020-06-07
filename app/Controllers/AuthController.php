<?php

namespace App\Controllers;

use App\DB\DB;
use App\Enum;
use App\Templates;
use App\Util;

class AuthController
{
    protected $connection;
    protected $template;
    protected $util;

    public function __construct()
    {
        $this->connection = new DB();
        $this->template = new Templates();
        $this->util = new Util();
    }
    
    public function login()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $tipo = $data['tipo'];
        $email = $data['email'];
        $password = $data['password'];

        $turma = ($tipo === Enum::TIPO_ALUNO) ? ', a.turma AS turma_atual' : '';

        $alias = substr($tipo, 0, 1);

        $query = "
            SELECT u.*, $alias.id AS $tipo $turma
                FROM usuario u
                JOIN $tipo $alias ON $alias.usuario = u.id
                WHERE u.id = $alias.usuario
                AND u.email = '$email'
        ";
    
        $usersQuery = $this->connection->query($query);

        $user = $usersQuery->fetchObject();
        
        $msg = '';
        $redirect = '';
        
        if ($user) {
            $actualPassword = $user->pass;
            $password = md5($password . $user->salt);

            if ($password == $actualPassword) {
                if ($user->endereco) {
                    $enderecoQuery = $this->connection->query("
                        SELECT * from endereco where id = $user->endereco
                    ");

                    $endereco = $enderecoQuery->fetchObject();
                    $user->endereco = $endereco;
                }

                session_start();
                $_SESSION['user'] = $user;
                $_SESSION['tipo'] = $tipo;

                $url = "$tipo/home";
                
                $msg = 'Bem-vindo!';
                $redirect = "<p/><a href='$url'>Ir</a> para minha tela inicial.";
            } else {
                $msg = 'Usuário não cadastrado ou senha incorreta!';
                $redirect = "<p/><a href='../webschool/'>Voltar</a>";
            }

            $args = array(
                'msg' => $msg,
                'redirect' => $redirect
            );

            $this->util->loadTemplate('login.html', $args);
        }
    }
    
    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: /webschool/');
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
}
