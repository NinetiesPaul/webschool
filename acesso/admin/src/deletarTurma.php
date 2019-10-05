<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';

$id = $_GET['id'];

$user = $db->prepare("DELETE FROM turma WHERE idTurma=:user_id");

$user->execute([
    'user_id' => $id,
]);

header('Location: ../turma');
