<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\DB\DB;
use App\Templates;

/**
 * Description of AuthController
 *
 * @author Paul Richard
 */
class AuthController
{
    protected $connection;
    
    protected $template;
    
    public function __construct()
    {
        $this->connection = new DB();
        $this->template = new Templates();
    }
    
    public function login()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $tipo = $data['tipo'];
        $email = $data['email'];
        $password = $data['password'];
    
        $usersQuery = $this->connection->query("
            SELECT usuario.*, $tipo.id AS $tipo FROM usuario, $tipo
            WHERE usuario.id=$tipo.usuario
            AND usuario.email='$email'
            ");

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

            $arrTags = array(
                'msg' => $msg,
                'redirect' => $redirect
            );

            $template = $this->template->getTemplate('login.html');
            $templateFinal = $this->template->parseTemplate($template, $arrTags);

            echo $templateFinal;
        }
    }
    
    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: /webschool/');
    }
}
