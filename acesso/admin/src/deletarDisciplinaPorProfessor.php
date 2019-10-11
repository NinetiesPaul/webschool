<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';

$id = $_GET['id'];

$user = $db->prepare("DELETE FROM disciplina_por_professor WHERE id=:id");

$user->execute([
    'id' => $id,
]);

header('Location: ../professor');
