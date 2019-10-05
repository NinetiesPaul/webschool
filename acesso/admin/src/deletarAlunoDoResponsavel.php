<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';
        
$id = $_GET['resp'];
$aluno = $_GET['aluno'];

$user = $db->prepare("
    delete from responsavelporaluno
    where responsavelporaluno.idresponsavel in (select idresponsavel from responsavel where idUsuario=:user_id)
    and responsavelporaluno.idaluno in (select idaluno from aluno where idUsuario=:idaluno)
");

$user->execute([
    'user_id' => $id,
    'idaluno' => $aluno,
]);

header("Location: ../../responsavel/$id");
