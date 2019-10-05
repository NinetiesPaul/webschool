<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';

$id = $_GET['id'];

$aluno = $db->prepare("DELETE FROM aluno WHERE idUsuario=:user_id");

$aluno->execute([
    'user_id' => $id,
]);

$avatarQuery = $db->query("select * from fotosdeavatar where idUsuario=$id");
$avatar = $avatarQuery->fetchObject();

if ($avatar) {
    unlink('../../'.$avatarQuery->imagemThumbUrl);
    unlink('../../'.$avatarQuery->imagemUrl);
}

$avatar = $db->prepare("DELETE FROM fotosdeavatar WHERE idUsuario=:user_id");

$avatar->execute([
    'user_id' => $id,
]);

$user = $db->prepare("DELETE FROM usuario WHERE idUsuario=:user_id");

$user->execute([
    'user_id' => $id,
]);

header('Location: ../aluno');
