<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';

$userId = $_SESSION['user_id'];

$idDisciplina = $_POST['id'];
$nome = $_POST['nome'];

$user = $db->prepare("
    UPDATE disciplina
    SET nomeDisciplina=:nome
    where idDisciplina=:idDisciplina
    ");

$user->execute([
    'nome' => $nome,
    'idDisciplina' => $idDisciplina,
]);

header('Location: ../disciplina');
