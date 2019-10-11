<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';
include '../../../data/functions.php';


$email = $_POST['email'];

$exists = verificarLoginOnPost('professor', $email);

if ($exists) {
    header('Location: ../professor');
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

$professor = $db->prepare("INSERT INTO professor (usuario) VALUES (:idUusuario)");
$professor->execute([
    'idUusuario' => $userId,
]);

$avatar = $db->prepare("INSERT INTO fotosdeavatar (usuario) VALUES (:idUusuario)");
$avatar->execute([
    'idUusuario' => $userId,
]);

header('Location: ../professor');
