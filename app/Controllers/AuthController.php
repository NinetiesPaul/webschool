<?php

namespace App\Controllers;

use App\DB\Storage\EnderecoStorage;
use App\DB\Storage\UsuarioStorage;
use App\Enum;
use App\Templates;
use App\Util;

class AuthController
{
    protected $enderecoStorage;
    protected $usuarioStorage;
    protected $template;
    protected $util;
    protected $folder;

    public function __construct()
    {
        $this->enderecoStorage = new EnderecoStorage();
        $this->usuarioStorage = new UsuarioStorage();
        $this->template = new Templates();
        $this->folder = getenv('FOLDER', '');
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

        $user = $this->usuarioStorage->verificarUsuario($alias, $turma, $tipo, $email);
        
        $msg = '';
        $redirect = '';
        
        if ($user) {
            $actualPassword = $user->pass;
            $password = md5($password . $user->salt);

            if ($password == $actualPassword) {
                if ($user->endereco) {
                    $user->endereco = $this->enderecoStorage->verEndereco($user->endereco);
                }

                session_start();
                $_SESSION['user'] = $user;
                $_SESSION['tipo'] = $tipo;

                $url = "$tipo/home";
                
                $msg = 'Bem-vindo!';
                $redirect = "<p/><a href='$url'>Ir</a> para minha tela inicial.";
            } else {
                $msg = 'Usuário não cadastrado ou senha incorreta!';
                $redirect = "<p/><a href='..$this->folder/'>Voltar</a>";
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
        header('Location: /');
    }

    public function loginTaken()
    {
        $data = json_decode(json_encode($_POST), true);

        $login = $data["login"];
        $tipo = $data["tipo"];
        $id = (isset($data["id"])) ? (int) $data['id'] : null;

        $res = $this->usuarioStorage->loginTaken($login, $tipo, $id);

        echo json_encode([
            'loginTaken' => $res
        ]);
    }
}
