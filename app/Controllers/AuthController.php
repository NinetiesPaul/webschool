<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\DB\DB;

/**
 * Description of AuthController
 *
 * @author Paul Richard
 */
class AuthController {
    
    protected $connection;
    
    public function __construct() {
        $this->connection = new DB();
    }
    
    public function login()
    {
        echo self::connection;exit;
        
        $data = json_decode(json_encode($_POST), true);
        
        $tipo = $_POST['tipo'];
        $email = $_POST['email'];
        $password = $_POST['password'];
    
    
        $user = $usersQuery->fetchObject();

        echo $user; exit;
        
        $msg = '';
        $redirect = '';
        
        if ($user) {
            $actualPassword = $user->pass;
            $password = md5($password . $user->salt);

            if ($password == $actualPassword) {

                if ($user->endereco) {
                    $enderecoQuery = $db->query("
                        SELECT * from endereco where id = $user->endereco
                    ");

                    $endereco = $enderecoQuery->fetchObject();
                    $user->endereco = $endereco;
                }

                /*echo "<pre>";
                print_r($user);
                echo "</pre>";*/

                session_start();
                $_SESSION['user'] = $user;
                $_SESSION['tipo'] = $tipo;

                $msg = 'Bem-vindo!';
                $redirect = "<p/><a href='home'>Ir</a> para minha tela inicial.";
            } else {
                $msg = 'Usuário não cadastrado ou senha incorreta!';
                $redirect = "<p/><a href='home'>Voltar</a>";
            }


            $arrTags = array(
                'status'=> $status
            );

            $template = self::getTemplate('login.html');
            $templateFinal 	= self::parseTemplate( $template, $arrTags );

            echo $templateFinal;
        }
    }
    
    private function getTemplate( $template, $folder = "web/" ) 
    {
        $arqTemp = $folder.$template; // criando var com caminho do arquivo
        $content = '';

        if ( is_file( $arqTemp ) ) // verificando se o arq existe
            $content = file_get_contents( $arqTemp ); // retornando conteúdo do arquivo

        return $content;
    }
    
    private function parseTemplate( $template, $array ) 
    {
        foreach ($array as $a => $b) {// recebemos um array com as tags 
            if (strpos ($a, 'list')) {
                $template = str_replace( '{'.$a.'}', json_encode($b), $template );
            } else {
                $template = str_replace( '{'.$a.'}', $b, $template );
            }
        }

        return $template; // retorno o html com conteúdo final
    }
}
