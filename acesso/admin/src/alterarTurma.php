<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';

$userId = $_SESSION['user_id'];

$turmaId = $_POST['id'];
$nome = $_POST['nome'];
$serie = $_POST['serie'];

$user = $db->prepare("
    UPDATE turma
    SET serie=:serie, nomeTurma=:nome
    where idTurma=:turmaId
    ");

$user->execute([
    'nome' => $nome,
    'serie' => $serie,
    'turmaId' => $turmaId,
]);

header('Location: ../turma');
