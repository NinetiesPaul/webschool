<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';

$serie = $_POST['serie'];
$nome = $_POST['nome'];

$user = $db->prepare("INSERT INTO turma (nomeTurma, serie)
        VALUES (:nome, :serie)");

$count = $user->execute([
    'nome' => $nome,
    'serie' => $serie,
]);

$userId = (int) $db->lastInsertId();

header('Location: ../turma');
