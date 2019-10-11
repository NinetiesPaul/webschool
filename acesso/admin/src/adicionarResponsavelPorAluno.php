<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../includes/php/conn.php';

$responsavel = $_POST['id'];
$aluno = $_POST['aluno'];

$responsavelQuery = $db->query("select id from responsavel where usuario=$responsavel");
$responsavelQuery = $responsavelQuery->fetchObject();

$alunoQuery = $db->query("select id from aluno where usuario=$aluno");
$alunoQuery = $alunoQuery->fetchObject();

$user = $db->prepare("INSERT INTO responsavel_por_aluno (responsavel, aluno)
        VALUES (:responsavel, :aluno)");

$count = $user->execute([
    'responsavel' => $responsavelQuery->id,
    'aluno' => $alunoQuery->id,
]);
header("Location: ../responsavel/$responsavel");
