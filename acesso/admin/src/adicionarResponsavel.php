<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';
include '../../../data/functions.php';

$endereco = $db->prepare("INSERT INTO endereco (idEstado)
        VALUES (:estado)");

$endereco->execute([
    'estado' => 1,
]);

$idEndereco = (int) $db->lastInsertId();

$nome = $_POST['nome'];
$email = $_POST['email'];

$exists = verificarLoginOnPost('responsavel', $email);

if ($exists) {
    header('Location: ../responsavel');
    exit();
}

$password = $_POST['password'];
$salt = time() + rand(100, 1000);
$password = md5($password . $salt);
$turma = $_POST['turma'];

$user = $db->prepare("INSERT INTO usuario (nome, email, pass, salt, idEndereco)
        VALUES (:name, :email, :password, :salt, :endereco)");

$user->execute([
    'name' => $nome,
    'email' => $email,
    'password' => $password,
    'salt' => $salt,
    'endereco' => $idEndereco,
]);

$userId = (int) $db->lastInsertId();

$responsavel = $db->prepare("INSERT INTO responsavel (idUsuario) VALUES (:idUusuario)");
$responsavel->execute([
    'idUusuario' => $userId,
]);

$avatar = $db->prepare("INSERT INTO fotosdeavatar (idUsuario) VALUES (:idUusuario)");
$avatar->execute([
    'idUusuario' => $userId,
]);

header('Location: ../responsavel');
