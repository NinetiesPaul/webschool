<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/functions.php';
include '../../../data/conn.php';

$userId = $_POST['id'];
$nome = $_POST['nome'];
$email = $_POST['email'];

$exists = verificarLoginOnPost('responsavel', $email, $userId);

if ($exists) {
    header('Location: ../responsavel');
    exit();
}

$password = $_POST['password'];
$salt = $_POST['salt'];
$newPassword = md5($password);

$sql = "
    UPDATE usuario
    SET nome=:nome, email=:email
    ";

$fields = [
    'nome' => $nome,
    'email' => $email,
];

if (strlen($password) > 0) {
    $password = $_POST['password'];
    $password = md5($password . $salt);

    $sql .= ' ,pass=:pass';
    $fields['pass'] = $password;
}

$sql .= ' where id=:userId';
$fields['userId'] = $userId;

$user = $db->prepare($sql);
$user->execute($fields);

header('Location: ../responsavel');
