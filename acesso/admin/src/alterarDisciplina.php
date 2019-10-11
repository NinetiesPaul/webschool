<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';

$id = $_POST['id'];
$nome = $_POST['nome'];

$user = $db->prepare("
    UPDATE disciplina
    SET nome=:nome
    where id=:id
    ");

$user->execute([
    'nome' => $nome,
    'id' => $id,
]);

header('Location: ../disciplina');
