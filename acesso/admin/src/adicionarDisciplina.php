<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';

$nome = $_POST['nome'];

$user = $db->prepare("
    INSERT INTO disciplina (nomeDisciplina)
    VALUES (:nome)
");

$count = $user->execute([
    'nome' => $nome,
]);

$userId = (int) $db->lastInsertId();

header('Location: ../disciplina');
