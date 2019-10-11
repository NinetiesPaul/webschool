<?php

include '../data/functions.php';
include '../data/conn.php';

$tipo = (isset($_POST['tipo'])) ? $_POST['tipo'] : false;

switch ($tipo) {
    case 'usuario':
        $userId = $_POST['usuario'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $password = $_POST['senha'];
        $newPassword = md5($password);
        $tel1 = $_POST['telefone1'];
        $tel2 = $_POST['telefone2'];

        $user = $db->prepare("
            UPDATE usuario
            SET nome=:nome, email=:email, telefone1=:tel1, telefone2=:tel2
            where idUsuario=:userId
        ");

        $user->execute([
            'nome' => $nome,
            'email' => $email,
            'tel1' => $tel1,
            'tel2' => $tel2,
            'userId' => $userId,
        ]);
        
        session_start();
        $endereco = $_SESSION['user']->endereco;
        
        $user = (object) $_POST;
        $user->id = $userId;
        
        $user->endereco = $endereco;
        $_SESSION['user'] = $user;
        
        break;
        
    case 'endereco':
        $rua = $_POST['rua'];
        $numero = $_POST['numero'];
        $bairro = $_POST['bairro'];
        $complemento = $_POST['complemento'];
        $cidade = $_POST['cidade'];
        $cep = $_POST['cep'];
        $estado = $_POST['estado'];
        $endereco = $_POST['id'];

        
        
        $user = $db->prepare("
            UPDATE endereco
            SET rua=:rua, numero=:numero, bairro=:bairro, complemento=:complemento, cidade=:cidade, cep=:cep, estado=:estado
            where id=:endereco
        ");
        

        $user->execute([
            'rua' => $rua,
            'numero' => $numero,
            'bairro' => $bairro,
            'complemento' => $complemento,
            'cidade' => $cidade,
            'cep' => $cep,
            'estado' => $estado,
            'endereco' => $endereco,
        ]);
        
        unset($_POST['tipo']);
        $endereco = (object) $_POST;
        
        session_start();
        $user = $_SESSION['user'];
        $user->endereco = $endereco;
        $_SESSION['user'] = $user;
        
        break;
    
    case false:
        break;
}

header('Location: perfil');
