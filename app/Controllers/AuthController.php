<?php

namespace App\Controllers;

use App\DB\Storage\EnderecoStorage;
use App\DB\Storage\UsuarioStorage;
use App\Enum;
use App\ErrorHandler;
use App\Templates;
use App\Util;

class AuthController
{
    protected $enderecoStorage;
    protected $usuarioStorage;

    public function __construct()
    {
        $this->enderecoStorage = new EnderecoStorage();
        $this->usuarioStorage = new UsuarioStorage();
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

                header("Location: $url");
            }
        }

        new ErrorHandler('Usuário não cadastrado ou senha incorreta!', "/");
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
