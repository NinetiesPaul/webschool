<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';

$responsavel = $_POST['id'];
$aluno = $_POST['aluno'];

$responsavelQuery = $db->query("select idresponsavel from responsavel where idUsuario=$responsavel");
$responsavelQuery = $responsavelQuery->fetchObject();

$alunoQuery = $db->query("select idaluno from aluno where idUsuario=$aluno");
$alunoQuery = $alunoQuery->fetchObject();

$user = $db->prepare("INSERT INTO responsavelporaluno (idresponsavel, idaluno)
        VALUES (:responsavel, :aluno)");

$count = $user->execute([
    'responsavel' => $responsavelQuery->idresponsavel,
    'aluno' => $alunoQuery->idaluno,
]);
header("Location: ../responsavel/$responsavel");
