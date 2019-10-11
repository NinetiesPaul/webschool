<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../includes/php/conn.php';

$id = $_GET['id'];

$user = $db->prepare("DELETE FROM disciplina WHERE id=:id");

$user->execute([
    'id' => $id,
]);

header('Location: ../disciplina');
