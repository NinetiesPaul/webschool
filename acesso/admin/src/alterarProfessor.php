<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/functions.php';
include '../../../data/conn.php';

$userId = $_SESSION['user_id'];

$userId = $_POST['id'];
$nome = $_POST['nome'];
$email = $_POST['email'];

$exists = verificarLoginOnPost('professor', $email, $userId);

if ($exists) {
    header('Location: ../professor');
    exit();
}

$password = $_POST['password'];
$salt = $_POST['salt'];
$newPassword = md5($password);


$sql = "
    UPDATE usuario
    SET nome=:nome, email=:email";

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

$fields['userId'] = $userId;
$sql .= ' where idUsuario=:userId';

$professorQuery = $db->prepare($sql);
$professorQuery->execute($fields);

header('Location: ../professor');
