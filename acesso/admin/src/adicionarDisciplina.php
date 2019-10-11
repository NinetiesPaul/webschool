<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../includes/php/conn.php';

$nome = $_POST['nome'];

$user = $db->prepare("
    INSERT INTO disciplina (nome)
    VALUES (:nome)
");

$count = $user->execute([
    'nome' => $nome,
]);

header('Location: ../disciplina');
