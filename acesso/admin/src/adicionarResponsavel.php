<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../includes/php/conn.php';
include '../../../includes/php/functions.php';

$email = $_POST['email'];
$exists = verificarLoginOnPost('responsavel', $email);
if ($exists) {
    header('Location: ../responsavel');
    exit();
}

$endereco = $db->prepare("INSERT INTO endereco (estado)
        VALUES (:estado)");

$endereco->execute([
    'estado' => 1,
]);

$idEndereco = (int) $db->lastInsertId();

$nome = $_POST['nome'];
$password = $_POST['password'];
$salt = time() + rand(100, 1000);
$password = md5($password . $salt);

$user = $db->prepare("INSERT INTO usuario (nome, email, pass, salt, endereco)
        VALUES (:name, :email, :password, :salt, :endereco)");

$user->execute([
    'name' => $nome,
    'email' => $email,
    'password' => $password,
    'salt' => $salt,
    'endereco' => $idEndereco,
]);

$userId = (int) $db->lastInsertId();

$responsavel = $db->prepare("INSERT INTO responsavel (usuario) VALUES (:idUusuario)");
$responsavel->execute([
    'idUusuario' => $userId,
]);

$avatar = $db->prepare("INSERT INTO fotos_de_avatar (usuario) VALUES (:idUusuario)");
$avatar->execute([
    'idUusuario' => $userId,
]);

header('Location: ../responsavel');
