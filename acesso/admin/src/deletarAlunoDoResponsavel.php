<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../includes/php/conn.php';
        
$id = $_GET['resp'];
$aluno = $_GET['aluno'];

$user = $db->prepare("
    delete from responsavel_por_aluno
    where responsavel_por_aluno.responsavel in (select id from responsavel where usuario=:user_id)
    and responsavel_por_aluno.aluno in (select id from aluno where usuario=:idaluno)
");

$user->execute([
    'user_id' => $id,
    'idaluno' => $aluno,
]);

header("Location: ../../responsavel/$id");
