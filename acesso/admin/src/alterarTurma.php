<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';

$turma = $_POST['id'];
$nome = $_POST['nome'];
$serie = $_POST['serie'];

$user = $db->prepare("
    UPDATE turma
    SET serie=:serie, nome=:nome
    where id=:turma
    ");

$user->execute([
    'nome' => $nome,
    'serie' => $serie,
    'turma' => $turma,
]);

header('Location: ../turma');
